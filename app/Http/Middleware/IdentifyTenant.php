<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(
        private readonly TenantContext $tenantContext
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->tenantContext->forget();

        if ($request->user()?->tenant_id) {
            $tenant = Tenant::query()->find($request->user()->tenant_id);
            if ($tenant) {
                $this->tenantContext->set($tenant);
            }

            return $next($request);
        }

        if ($this->isCentralHost($request)) {
            return $next($request);
        }

        $tenant = $this->resolveFromRoute($request)
            ?? $this->resolveFromHeader($request)
            ?? $this->resolveFromSubdomain($request);

        if ($tenant !== null) {
            $this->tenantContext->set($tenant);
        }

        return $next($request);
    }

    private function isCentralHost(Request $request): bool
    {
        $host = $this->normalizeHost($request->getHost());

        foreach (config('tenancy.central_domains', []) as $allowed) {
            if ($host === $this->normalizeHost((string) $allowed)) {
                return true;
            }
        }

        return false;
    }

    private function resolveFromRoute(Request $request): ?Tenant
    {
        $param = config('tenancy.slug_route_parameter', 'tenant');
        $value = $request->route($param);

        if ($value instanceof Tenant) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return Tenant::query()->where('slug', $value)->first();
        }

        return null;
    }

    private function resolveFromHeader(Request $request): ?Tenant
    {
        $header = config('tenancy.header_slug', 'X-Tenant-Slug');
        $slug = $request->header($header);

        if (! is_string($slug) || $slug === '') {
            return null;
        }

        return Tenant::query()->where('slug', $slug)->first();
    }

    private function resolveFromSubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $base = (string) config('tenancy.base_domain', 'localhost');

        if ($host === $base) {
            return null;
        }

        $suffix = '.'.$base;
        if (! str_ends_with($host, $suffix)) {
            return null;
        }

        $subdomain = substr($host, 0, -strlen($suffix));
        if ($subdomain === '' || str_contains($subdomain, '.')) {
            return null;
        }

        return Tenant::query()->where('slug', $subdomain)->first();
    }

    private function normalizeHost(string $host): string
    {
        return strtolower($host);
    }
}
