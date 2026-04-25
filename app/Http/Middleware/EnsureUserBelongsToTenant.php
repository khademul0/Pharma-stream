<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = $request->route('tenant');

        if (! $user || ! $tenant instanceof Tenant) {
            abort(403);
        }

        if ((int) $user->tenant_id !== (int) $tenant->getKey()) {
            abort(403);
        }

        return $next($request);
    }
}
