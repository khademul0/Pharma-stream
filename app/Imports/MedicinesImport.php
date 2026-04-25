<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Supplier;
use App\Tenancy\TenantContext;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicinesImport implements ToCollection, WithHeadingRow
{
    public function __construct(
        private readonly TenantContext $tenantContext
    ) {}

    public function collection(Collection $rows): void
    {
        $tenantId = $this->tenantContext->id();
        if (! $tenantId) {
            throw new \RuntimeException('Tenant context missing.');
        }

        $category = MedicineCategory::query()->orderBy('id')->firstOrFail();

        foreach ($rows as $row) {
            $row = is_array($row) ? $row : $row->toArray();
            $name = $row['name'] ?? $row['medicine_name'] ?? null;
            if (! $name) {
                continue;
            }

            $medicine = Medicine::query()->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'name' => $name,
                ],
                [
                    'generic_name' => $row['generic_name'] ?? null,
                    'category_id' => $category->id,
                    'barcode' => $row['barcode'] ?? null,
                    'unit_type' => in_array(strtolower(trim($row['unit_type'] ?? '')), ['box', 'strip', 'tablet']) ? strtolower(trim($row['unit_type'] ?? '')) : 'tablet',
                    'conversion_rate' => (float) ($row['conversion_rate'] ?? 1),
                    'units_per_strip' => (int) ($row['units_per_strip'] ?? 10),
                    'strips_per_box' => (int) ($row['strips_per_box'] ?? 1),
                    'low_stock_threshold' => isset($row['low_stock_threshold']) ? (int) $row['low_stock_threshold'] : null,
                ]
            );

            $supplierName = $row['supplier_name'] ?? 'Default Supplier';
            $supplier = Supplier::query()->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'name' => $supplierName,
                ],
                [
                    'contact_person' => null,
                    'phone' => $row['supplier_phone'] ?? null,
                ]
            );

            Batch::query()->create([
                'tenant_id' => $tenantId,
                'medicine_id' => $medicine->id,
                'supplier_id' => $supplier->id,
                'batch_number' => (string) ($row['batch_number'] ?? 'INIT-'.uniqid()),
                'expiry_date' => $row['expiry_date'] ?? now()->addYear()->toDateString(),
                'cost_price' => (float) ($row['cost_price'] ?? 0),
                'selling_price' => (float) ($row['selling_price'] ?? 0),
                'current_stock_qty' => (float) ($row['stock_qty'] ?? $row['stock'] ?? 0),
            ]);
        }
    }
}
