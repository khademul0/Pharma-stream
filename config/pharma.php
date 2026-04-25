<?php

return [

    'tax_rate' => (float) env('PHARMA_TAX_RATE', 0.1),

    'expiry_alert_days' => (int) env('PHARMA_EXPIRY_ALERT_DAYS', 90),

    'chronic_supply_days' => (int) env('PHARMA_CHRONIC_SUPPLY_DAYS', 30),

    'chronic_reminder_before_days' => (int) env('PHARMA_CHRONIC_REMINDER_BEFORE_DAYS', 3),

];
