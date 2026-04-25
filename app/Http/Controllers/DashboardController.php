<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\RefillReminder;
use App\Models\Sale;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $days = (int) config('pharma.expiry_alert_days', 90);

        $expiringBatches = Batch::query()
            ->with('medicine')
            ->whereDate('expiry_date', '<=', now()->addDays($days)->toDateString())
            ->where('current_stock_qty', '>', 0)
            ->orderBy('expiry_date')
            ->limit(25)
            ->get()
            ->map(fn (Batch $b) => [
                'id' => $b->id,
                'medicine' => $b->medicine->name,
                'batch_number' => $b->batch_number,
                'expiry_date' => $b->expiry_date->toDateString(),
                'stock' => (float) $b->current_stock_qty,
            ]);

        $lowStockMedicines = Medicine::query()
            ->select('medicines.*')
            ->selectSub(function ($q) {
                $q->from('batches')
                    ->selectRaw('COALESCE(SUM(current_stock_qty), 0)')
                    ->whereColumn('batches.medicine_id', 'medicines.id');
            }, 'stock_sum')
            ->whereNotNull('low_stock_threshold')
            ->havingRaw('stock_sum <= medicines.low_stock_threshold')
            ->orderBy('name')
            ->limit(25)
            ->get()
            ->map(fn (Medicine $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'threshold' => (int) $m->low_stock_threshold,
                'stock' => (float) ($m->stock_sum ?? 0),
            ]);

        $upcomingRefills = RefillReminder::query()
            ->with(['customer', 'medicine'])
            ->whereNull('acknowledged_at')
            ->whereDate('remind_at', '<=', now()->addDays(7)->toDateString())
            ->orderBy('remind_at')
            ->limit(25)
            ->get()
            ->map(fn (RefillReminder $r) => [
                'id' => $r->id,
                'customer' => $r->customer->name,
                'medicine' => $r->medicine->name,
                'remind_at' => $r->remind_at->toDateString(),
            ]);

        $todaySales = Sale::query()
            ->whereDate('sold_at', today())
            ->sum('total');

        return Inertia::render('Dashboard', [
            'expiringBatches' => $expiringBatches,
            'lowStockMedicines' => $lowStockMedicines,
            'upcomingRefills' => $upcomingRefills,
            'todaySales' => (float) $todaySales,
        ]);
    }
}
