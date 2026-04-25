<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'generic_name',
        'category_id',
        'barcode',
        'unit_type',
        'conversion_rate',
        'units_per_strip',
        'strips_per_box',
        'low_stock_threshold',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'conversion_rate' => 'decimal:4',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function scopeSearch($query, string $term)
    {
        $term = '%'.$term.'%';

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)
                ->orWhere('generic_name', 'like', $term)
                ->orWhere('barcode', 'like', $term);
        });
    }
}
