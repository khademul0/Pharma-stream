<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function zReport(Request $request): Response
    {
        $date = $request->date('date') ?? today();

        $sales = Sale::query()
            ->whereDate('sold_at', $date)
            ->get();

        return Inertia::render('Reports/ZReport', [
            'date' => $date->toDateString(),
            'count' => $sales->count(),
            'subtotal' => (float) $sales->sum('subtotal'),
            'tax' => (float) $sales->sum('tax'),
            'discount' => (float) $sales->sum('discount'),
            'total' => (float) $sales->sum('total'),
        ]);
    }

    public function profitLoss(Request $request): Response
    {
        $from = Carbon::parse($request->input('from', today()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', today()->toDateString()))->endOfDay();

        $rows = SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('batches', 'batches.id', '=', 'sale_items.batch_id')
            ->whereBetween('sales.sold_at', [$from, $to])
            ->select([
                DB::raw('SUM(sale_items.line_total) as revenue'),
                DB::raw('SUM(sale_items.quantity_base_units * batches.cost_price) as cost'),
            ])
            ->first();

        $revenue = (float) ($rows->revenue ?? 0);
        $cost = (float) ($rows->cost ?? 0);

        return Inertia::render('Reports/ProfitLoss', [
            'from' => $request->input('from', today()->startOfMonth()->toDateString()),
            'to' => $request->input('to', today()->toDateString()),
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $revenue - $cost,
        ]);
    }

    public function tax(Request $request): Response
    {
        $from = Carbon::parse($request->input('from', today()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', today()->toDateString()))->endOfDay();

        $tax = Sale::query()
            ->whereBetween('sold_at', [$from, $to])
            ->sum('tax');

        return Inertia::render('Reports/TaxReport', [
            'from' => $request->input('from', today()->startOfMonth()->toDateString()),
            'to' => $request->input('to', today()->toDateString()),
            'tax' => (float) $tax,
        ]);
    }
}
