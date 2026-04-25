<?php

namespace App\Console\Commands;

use App\Models\RefillReminder;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Console\Command;

class PharmaRefillRemindersCommand extends Command
{
    protected $signature = 'pharma:refill-reminders';

    protected $description = 'Process chronic refill reminders due today (logged for follow-up).';

    public function handle(TenantContext $tenantContext): int
    {
        foreach (Tenant::query()->orderBy('id')->get() as $tenant) {
            $tenantContext->set($tenant);

            $due = RefillReminder::query()
                ->with(['customer', 'medicine'])
                ->whereNull('acknowledged_at')
                ->whereDate('remind_at', '<=', today())
                ->get();

            foreach ($due as $reminder) {
                $this->line(sprintf(
                    '[%s] [REFILL] %s — %s (due %s)',
                    $tenant->slug,
                    $reminder->customer->name,
                    $reminder->medicine->name,
                    $reminder->remind_at->toDateString()
                ));
            }
        }

        $tenantContext->forget();

        $this->info('Done.');

        return self::SUCCESS;
    }
}
