<?php

namespace App\Http\Controllers;

use App\Imports\MedicinesImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class InventoryImportController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Inventory/Import');
    }

    public function store(Request $request, MedicinesImport $import): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        Excel::import($import, $request->file('file'));

        return redirect()->route('inventory.import')->with('success', 'Import processed.');
    }
}
