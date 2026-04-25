<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'is_chronic',
        'credit_limit',
        'balance_due',
    ];

    protected function casts(): array
    {
        return [
            'is_chronic' => 'boolean',
            'credit_limit' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
