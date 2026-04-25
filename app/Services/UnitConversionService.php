<?php

namespace App\Services;

use App\Models\Medicine;

class UnitConversionService
{
    public function toBaseUnits(Medicine $medicine, string $soldUnit, float $quantity): float
    {
        $ups = max(1, (int) $medicine->units_per_strip);
        $spb = max(1, (int) $medicine->strips_per_box);

        return match ($soldUnit) {
            'tablet' => $quantity,
            'strip' => $quantity * $ups,
            'box' => $quantity * $spb * $ups,
            default => $quantity,
        };
    }
}
