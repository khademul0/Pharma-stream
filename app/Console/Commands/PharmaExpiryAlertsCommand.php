<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Console\Command;

class PharmaExpiryAlertsCommand extends Command
{
    protected $signature = 'pharma:expiry-alerts';

    protected $description = 'Flag batches expiring within the configured alert window (logged for ops / notifications).';

    public function handle(TenantContext $tenantContext): int
    {
        $days = (int) config('pharma.expiry_alert_days', 90);

        foreach (Tenant::query()->orderBy('id')->get() as $tenant) {
            $tenantContext->set($tenant);

            $batches = Batch::query()
                ->with('medicine')
                ->where('current_stock_qty', '>', 0)
                ->whereDate('expiry_date', '<=', now()->addDays($days)->toDateString())
                ->orderBy('expiry_date')
                ->get();

            foreach ($batches as $batch) {
                $this->line(sprintf(
                    '[%s] [EXPIRY] %s | batch %s | expires %s | stock %s',
                    $tenant->slug,
                    $batch->medicine->name,
                    $batch->batch_number,
                    $batch->expiry_date->toDateString(),
                    $batch->current_stock_qty
                ));
            }
        }

        $tenantContext->forget();

        $this->info('Done.');

        return self::SUCCESS;
    }
}
