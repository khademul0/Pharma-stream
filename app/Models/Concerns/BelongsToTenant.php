<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $context = app(TenantContext::class);
            $column = $builder->getModel()->qualifyColumn('tenant_id');

            if ($context->id() !== null) {
                $builder->where($column, $context->id());

                return;
            }

            if (config('tenancy.strict_tenant_scope', true)) {
                $builder->whereRaw('0 = 1');
            }
        });

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);

            if ($model->getAttribute('tenant_id') !== null) {
                return;
            }

            if ($context->id() !== null) {
                $model->setAttribute('tenant_id', $context->id());
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId);
    }
}
