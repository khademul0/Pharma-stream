<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Models\SupplierReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierReturnController extends Controller
{
    public function index(): Response
    {
        $returns = SupplierReturn::query()
            ->with(['supplier', 'medicine', 'batch'])
            ->orderByDesc('id')
            ->paginate(25);

        return Inertia::render('Returns/Index', [
            'returns' => $returns,
            'suppliers' => Supplier::query()->orderBy('name')->get(),
            'medicines' => Medicine::query()->orderBy('name')->get(),
            'batches' => Batch::query()->with('medicine')->orderByDesc('id')->limit(200)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'medicine_id' => ['required', 'exists:medicines,id'],
            'batch_id' => ['nullable', 'exists:batches,id'],
            'quantity_base_units' => ['required', 'numeric', 'min:0.0001'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $data['batch_id'] = $data['batch_id'] ?: null;

        $batch = null;
        if (! empty($data['batch_id'])) {
            $batch = Batch::query()->findOrFail($data['batch_id']);
            if ((float) $batch->current_stock_qty < (float) $data['quantity_base_units']) {
                return back()->with('error', 'Insufficient stock in batch.');
            }

            $batch->current_stock_qty = (float) $batch->current_stock_qty - (float) $data['quantity_base_units'];
            $batch->save();
        }

        SupplierReturn::query()->create([
            'supplier_id' => $data['supplier_id'],
            'medicine_id' => $data['medicine_id'],
            'batch_id' => $batch?->id,
            'quantity_base_units' => $data['quantity_base_units'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('returns.index')->with('success', 'Return recorded.');
    }
}
