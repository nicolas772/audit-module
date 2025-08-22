<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtén el tenant desde la ruta /tenants/{tenant} o header X-Tenant-Id
        $tenantId = $request->header('X-Tenant-Id') ?? $request->route('tenant');
        app()->instance('currentTenantId', $tenantId);

        return $next($request);
    }
}
