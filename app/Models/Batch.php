<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'medicine_id',
        'supplier_id',
        'batch_number',
        'expiry_date',
        'cost_price',
        'selling_price',
        'current_stock_qty',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'cost_price' => 'decimal:4',
            'selling_price' => 'decimal:4',
            'current_stock_qty' => 'decimal:4',
        ];
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
