<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Auditing\Resolvers\TenantResolver;

class AuditTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $tenant)
    {
        // Puedes hacer una comprobación si deseas validar tenant o permiso

        // Lista de tablas _audit disponibles
        $tables = [
            'users_audit' => 'Usuarios',
            'courses_audit' => 'Cursos',
            'course_enrollments_audit' => 'Inscripciones',
        ];

        $tenantId  = TenantResolver::resolve();
        \Log::info("Tenant ID: " . $tenantId);

        return response()->json($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
