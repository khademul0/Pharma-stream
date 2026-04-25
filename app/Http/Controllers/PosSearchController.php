<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['items' => []]);
        }

        $medicines = Medicine::query()
            ->search($q)
            ->withSum('batches as stock_sum', 'current_stock_qty')
            ->limit(20)
            ->get();

        $items = $medicines->map(function (Medicine $m) {
            $stock = (float) ($m->stock_sum ?? 0);
            $alternatives = [];

            if ($stock <= 0 && $m->generic_name) {
                $alternatives = Medicine::query()
                    ->where('generic_name', $m->generic_name)
                    ->where('id', '!=', $m->id)
                    ->withSum('batches as stock_sum', 'current_stock_qty')
                    ->get()
                    ->filter(fn (Medicine $alt) => (float) ($alt->stock_sum ?? 0) > 0)
                    ->take(8)
                    ->map(fn (Medicine $alt) => [
                        'id' => $alt->id,
                        'name' => $alt->name,
                        'stock' => (float) ($alt->stock_sum ?? 0),
                    ])
                    ->values()
                    ->all();
            }

            return [
                'id' => $m->id,
                'name' => $m->name,
                'generic_name' => $m->generic_name,
                'barcode' => $m->barcode,
                'unit_type' => $m->unit_type,
                'units_per_strip' => (int) $m->units_per_strip,
                'strips_per_box' => (int) $m->strips_per_box,
                'stock' => $stock,
                'alternatives' => $alternatives,
            ];
        });

        return response()->json(['items' => $items]);
    }
}
