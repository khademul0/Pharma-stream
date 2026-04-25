<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BatchController extends Controller
{
    public function index(): Response
    {
        $batches = Batch::query()
            ->with(['medicine', 'supplier'])
            ->orderByDesc('id')
            ->paginate(25);

        return Inertia::render('Batches/Index', [
            'batches' => $batches,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Batches/Create', [
            'medicines' => Medicine::query()->orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'batch_number' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock_qty' => ['required', 'numeric', 'min:0'],
        ]);

        $data['supplier_id'] = $data['supplier_id'] ?: null;

        Batch::query()->create($data);

        return redirect()->route('batches.index')->with('success', 'Batch created.');
    }
}
