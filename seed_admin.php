<?php

$tenant = App\Models\Tenant::firstOrCreate(
    ['slug' => 'default'],
    ['name' => 'Default Clinic', 'plan_type' => 'standard']
);

App\Models\User::firstOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Admin',
        'password' => bcrypt('password'),
        'tenant_id' => $tenant->id,
    ]
);

echo "Tenant ID: {$tenant->id}\n";
echo "Admin user created: admin@admin.com / password\n";
