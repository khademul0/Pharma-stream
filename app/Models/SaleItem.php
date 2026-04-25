<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'sale_id',
        'medicine_id',
        'batch_id',
        'quantity_sold',
        'sold_unit',
        'quantity_base_units',
        'unit_price',
        'line_subtotal',
        'line_tax',
        'line_discount',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity_sold' => 'decimal:4',
            'quantity_base_units' => 'decimal:4',
            'unit_price' => 'decimal:4',
            'line_subtotal' => 'decimal:2',
            'line_tax' => 'decimal:2',
            'line_discount' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
