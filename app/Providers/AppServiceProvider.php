<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class, fn (): TenantContext => new TenantContext);
    }

    public function boot(): void
    {
        Route::bind('tenant', static function (string $value): Tenant {
            return Tenant::query()->where('slug', $value)->firstOrFail();
        });
    }
}
