<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MedicineController extends Controller
{
    public function index(): Response
    {
        $medicines = Medicine::query()
            ->with('category')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Medicines/Index', [
            'medicines' => $medicines,
        ]);
    }

    public function create(): Response
    {
        $categories = MedicineCategory::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Medicines/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'unit_type' => ['required', 'in:box,strip,tablet'],
            'conversion_rate' => ['required', 'numeric', 'min:0.0001'],
            'units_per_strip' => ['required', 'integer', 'min:1'],
            'strips_per_box' => ['required', 'integer', 'min:1'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Medicine::query()->create($data);

        return redirect()->route('medicines.index')->with('success', 'Medicine saved.');
    }

    public function edit(int $id): Response
    {
        $medicine = Medicine::findOrFail($id);
        $categories = MedicineCategory::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Medicines/Edit', [
            'medicine' => $medicine,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $medicine = Medicine::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'unit_type' => ['required', 'in:box,strip,tablet'],
            'conversion_rate' => ['required', 'numeric', 'min:0.0001'],
            'units_per_strip' => ['required', 'integer', 'min:1'],
            'strips_per_box' => ['required', 'integer', 'min:1'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $medicine->update($data);

        return redirect()->route('medicines.index')->with('success', 'Medicine updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicine deleted.');
    }
}
