<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tenant resolution
    |--------------------------------------------------------------------------
    |
    | Hosts listed in central_domains never resolve a tenant (marketing site,
    | central registration, etc.). For other hosts, the first subdomain segment
    | before base_domain is treated as the shop slug (e.g. acme.app.test).
    |
    */

    'base_domain' => env('TENANCY_BASE_DOMAIN', 'localhost'),

    'central_domains' => array_values(array_filter(array_map(
        static fn (string $host): string => trim($host),
        explode(',', (string) env('TENANCY_CENTRAL_DOMAINS', 'localhost,127.0.0.1'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Optional slug sources (checked after subdomain)
    |--------------------------------------------------------------------------
    */

    'slug_route_parameter' => 'tenant',

    'header_slug' => 'X-Tenant-Slug',

    /*
    |--------------------------------------------------------------------------
    | Query safety
    |--------------------------------------------------------------------------
    |
    | When true, models using BelongsToTenant return no rows if no tenant is
    | current (prevents accidental cross-tenant reads). Disable only for local
    | tooling if needed — prefer Model::withoutGlobalScope('tenant') instead.
    |
    */

    'strict_tenant_scope' => env('TENANCY_STRICT_SCOPE', true),

];
