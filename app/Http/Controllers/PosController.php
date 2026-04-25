<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        $customers = Customer::query()
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'is_chronic', 'credit_limit', 'balance_due']);

        return Inertia::render('POS/Index', [
            'customers' => $customers,
            'taxRate' => (float) config('pharma.tax_rate', 0.1),
        ]);
    }
}
