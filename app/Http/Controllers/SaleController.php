<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    public function index(Request $request): Response
    {
        $sales = Sale::query()
            ->with(['customer', 'user'])
            ->latest('sold_at')
            ->paginate(15);
            
        return Inertia::render('Sales/Index', [
            'sales' => $sales
        ]);
    }

    public function show(int $id): Response
    {
        $sale = Sale::findOrFail($id);
        
        // Ensure we load the necessary relations for the invoice
        $sale->load(['customer', 'user', 'items.medicine']);
        
        return Inertia::render('Sales/Show', [
            'sale' => $sale
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'lines.*.sold_unit' => ['required', 'in:tablet,strip,box'],
            'lines.*.qty' => ['required', 'numeric', 'min:0.0001'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_mode' => ['required', 'in:cash,credit'],
            'prescription' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $prescriptionPath = null;
        if ($request->hasFile('prescription')) {
            $prescriptionPath = $request->file('prescription')->store('prescriptions', 'public');
        }

        $taxRate = (float) config('pharma.tax_rate', 0.1);

        $sale = $this->saleService->createSale(
            $validated['lines'],
            $validated['customer_id'] ?? null,
            (float) ($validated['discount'] ?? 0),
            $prescriptionPath,
            $validated['payment_mode'],
            $taxRate
        );

        return redirect()->route('sales.show', $sale->id)->with('success', 'Sale completed.');
    }
}
