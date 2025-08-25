<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        if ($tenantId) {
            if (!Str::isUuid($tenantId)) {
                return response()->json(['message' => 'El tenant ID no es un UUID válido'], 400);
            }

            $tenantExists = Tenant::where('id', $tenantId)->exists();

            if (!$tenantExists) {
                Log::warning('[SetCurrentTenant] ID inválido recibido', ['tenantId' => $tenantId]);
                return response()->json(['message' => 'Tenant Inválido'], 403);
            }

            app()->instance('currentTenantId', $tenantId);
        } else {
            Log::warning('[SetCurrentTenant] No tenantId en header ni ruta');
            return response()->json(['message' => 'Tenant no especificado'], 400);
        }

        return $next($request);
    }
}
