<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class SetCurrentTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantIdRoute = $request->route('tenant');
        $tenantIdHeader = $request->header('X-Tenant-Id');
        $tenantId = $tenantIdRoute ?? $tenantIdHeader;
        
        \Log::info('[SetCurrentTenant] Incoming request', [
            'path' => $request->path(),
            //'method' => $request->method(),
            //'X-Tenant-Id' => $tenantIdHeader,
            //'tenantIdRoute' => $tenantIdRoute,
            //'tenantId' => $tenantId
        ]);

        if ($tenantId) {
            $tenantExists = Tenant::where('id', $tenantId)->exists();

            if (!$tenantExists) {
                Log::warning('[SetCurrentTenant] ID inválido recibido', ['tenantId' => $tenantId]);
                return response()->json(['message' => 'Invalid tenant'], 403);
            }

            app()->instance('currentTenantId', $tenantId);
            Log::info('[SetCurrentTenant] Tenant válido', ['tenantId' => $tenantId]);
        } else {
            Log::warning('[SetCurrentTenant] No tenantId en header ni ruta');
            return response()->json(['message' => 'Tenant not specified'], 400);
        }

        return $next($request);
    }
}
