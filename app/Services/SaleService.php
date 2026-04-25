<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\RefillReminder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Tenancy\TenantContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    public function __construct(
        private readonly UnitConversionService $units,
        private readonly TenantContext $tenantContext
    ) {}

    /**
     * @param  array<int, array{medicine_id:int, sold_unit:string, qty:float|int, unit_price:float}>  $lines
     */
    public function createSale(
        array $lines,
        ?int $customerId,
        float $discountTotal,
        ?string $prescriptionPath,
        string $paymentMode,
        float $taxRate
    ): Sale {
        $tenantId = (int) $this->tenantContext->id();
        if ($tenantId === 0) {
            throw new \RuntimeException('Tenant context missing.');
        }

        return DB::transaction(function () use ($lines, $customerId, $discountTotal, $prescriptionPath, $paymentMode, $taxRate, $tenantId) {
            $saleNumber = 'PS-'.$tenantId.'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));

            $subtotal = 0.0;
            $prepared = [];

            foreach ($lines as $line) {
                $medicine = Medicine::query()->lockForUpdate()->findOrFail((int) $line['medicine_id']);
                $qty = (float) $line['qty'];
                $soldUnit = (string) $line['sold_unit'];
                $unitPrice = (float) $line['unit_price'];
                $needBase = $this->units->toBaseUnits($medicine, $soldUnit, $qty);

                if ($needBase <= 0) {
                    throw new \InvalidArgumentException('Invalid quantity.');
                }

                $lineSubtotal = round($qty * $unitPrice, 2);
                $subtotal += $lineSubtotal;

                $prepared[] = [
                    'medicine' => $medicine,
                    'sold_unit' => $soldUnit,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'need_base' => $needBase,
                    'line_subtotal' => $lineSubtotal,
                ];
            }

            $taxable = max(0.0, $subtotal - $discountTotal);
            $tax = round($taxable * $taxRate, 2);
            $total = round($taxable + $tax, 2);

            $paidAmount = $total;
            $balanceDue = 0.0;
            $paymentStatus = 'paid';

            if ($paymentMode === 'credit') {
                if (! $customerId) {
                    throw new \InvalidArgumentException('Credit sales require a customer.');
                }
                $paidAmount = 0.0;
                $balanceDue = $total;
                $paymentStatus = 'credit';
            }

            /** @var Sale $sale */
            $sale = Sale::query()->create([
                'tenant_id' => $tenantId,
                'user_id' => auth()->id(),
                'customer_id' => $customerId,
                'sale_number' => $saleNumber,
                'subtotal' => round($subtotal, 2),
                'tax' => $tax,
                'discount' => round($discountTotal, 2),
                'total' => $total,
                'paid_amount' => $paidAmount,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus,
                'prescription_path' => $prescriptionPath,
                'sold_at' => now(),
            ]);

            foreach ($prepared as $row) {
                $medicine = $row['medicine'];
                $needBase = (float) $row['need_base'];
                $remaining = $needBase;

                $batches = Batch::query()
                    ->where('medicine_id', $medicine->id)
                    ->where('current_stock_qty', '>', 0)
                    ->orderBy('expiry_date')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($batches->isEmpty()) {
                    throw new \RuntimeException('No stock available for '.$medicine->name.'.');
                }

                $sumAvailable = (float) $batches->sum('current_stock_qty');
                if ($sumAvailable + 0.0001 < $needBase) {
                    throw new \RuntimeException('Insufficient stock for '.$medicine->name.'.');
                }

                $lineDiscountShare = $subtotal > 0
                    ? round($discountTotal * ($row['line_subtotal'] / $subtotal), 2)
                    : 0.0;
                $lineTaxable = max(0.0, $row['line_subtotal'] - $lineDiscountShare);
                $lineTax = round($lineTaxable * $taxRate, 2);
                $lineTotal = round($lineTaxable + $lineTax, 2);

                foreach ($batches as $batch) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $available = (float) $batch->current_stock_qty;
                    if ($available <= 0) {
                        continue;
                    }

                    $take = min($available, $remaining);
                    $portion = $needBase > 0 ? ($take / $needBase) : 0.0;

                    $batch->current_stock_qty = $available - $take;
                    $batch->save();

                    SaleItem::query()->create([
                        'tenant_id' => $tenantId,
                        'sale_id' => $sale->id,
                        'medicine_id' => $medicine->id,
                        'batch_id' => $batch->id,
                        'quantity_sold' => $take,
                        'sold_unit' => 'tablet',
                        'quantity_base_units' => $take,
                        'unit_price' => $row['unit_price'],
                        'line_subtotal' => round($row['line_subtotal'] * $portion, 2),
                        'line_tax' => round($lineTax * $portion, 2),
                        'line_discount' => round($lineDiscountShare * $portion, 2),
                        'line_total' => round($lineTotal * $portion, 2),
                    ]);

                    $remaining -= $take;
                }

                if ($remaining > 0.0001) {
                    throw new \RuntimeException('Stock allocation failed for '.$medicine->name.'.');
                }
            }

            if ($paymentMode === 'credit' && $customerId) {
                $customer = Customer::query()->lockForUpdate()->findOrFail($customerId);
                $customer->balance_due = round((float) $customer->balance_due + $total, 2);
                $customer->save();
            }

            if ($customerId) {
                $customer = Customer::query()->find($customerId);
                if ($customer?->is_chronic) {
                    $this->scheduleRefillReminders($sale, $customer, $lines);
                }
            }

            return $sale->fresh(['items.batch', 'items.medicine']);
        });
    }

    /**
     * @param  array<int, array{medicine_id:int}>  $lines
     */
    private function scheduleRefillReminders(Sale $sale, Customer $customer, array $lines): void
    {
        $supplyDays = (int) config('pharma.chronic_supply_days', 30);
        $beforeDays = (int) config('pharma.chronic_reminder_before_days', 3);
        $remindAt = Carbon::parse($sale->sold_at)->addDays($supplyDays - $beforeDays)->startOfDay();

        foreach ($lines as $line) {
            RefillReminder::query()->create([
                'tenant_id' => $sale->tenant_id,
                'customer_id' => $customer->id,
                'medicine_id' => (int) $line['medicine_id'],
                'sale_id' => $sale->id,
                'remind_at' => $remindAt,
            ]);
        }
    }
}
