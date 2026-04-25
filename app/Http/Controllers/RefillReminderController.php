<?php

namespace App\Http\Controllers;

use App\Models\RefillReminder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RefillReminderController extends Controller
{
    public function index(): Response
    {
        $reminders = RefillReminder::query()
            ->with(['customer', 'medicine'])
            ->orderBy('remind_at')
            ->paginate(30);

        return Inertia::render('Refills/Index', [
            'reminders' => $reminders,
        ]);
    }

    public function acknowledge(RefillReminder $refillReminder): RedirectResponse
    {
        $refillReminder->update([
            'acknowledged_at' => now(),
        ]);

        return redirect()->route('refills.index')->with('success', 'Reminder acknowledged.');
    }
}
