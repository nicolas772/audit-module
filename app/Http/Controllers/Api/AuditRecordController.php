<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AuditRecordRequest;
use App\Models\Audit\AuditTableMap;
use Illuminate\Support\Facades\Log;
use App\Enums\AuditActionType;

class AuditRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AuditRecordRequest $request): JsonResponse
    {
        try {
            // Las entidades seleccionadas llegarán desde front como table[] = *_audit
            $tableNames = $request->input('tables', []);

            if (!is_array($tableNames) || empty($tableNames)) {
                return response()->json(['message' => 'Debe especificar al menos una tabla de auditoría'], 400);
            }

            // Inicialización de estructura que almacena resultados
            $results = [];

            foreach ($tableNames as $table) {
                // Clase que almacena las tablas auditables y sus respectivos modelos
                $modelClass = AuditTableMap::resolve($table);

                if (! $modelClass || ! class_exists($modelClass)) {
                    Log::warning("Tabla no válida: $table");
                    continue;
                }
                
                // Se crea query a clase de auditoría(con global scope de tenant ya aplicado)
                $query = $modelClass::query();

                // Filtro por tipo (created, updated, deleted)
                if ($request->filled('type')) {
                    $type = AuditActionType::fromName($request->type);
                    $query->where('type', $type);
                }
                
                // Filtro por ID de objeto
                if ($request->filled('object_id')) {
                    $query->where('object_id', $request->object_id);
                }

                // Filtros por fecha (start_date)
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }

                // Filtros por fecha (end_date)
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }

                $perPage = $request->input('per_page', 15);
                $records = $query->orderByDesc('created_at')->paginate($perPage);

                $results[$table] = $records;
            }

            return response()->json($results);


        } catch (\Throwable $e) {
            Log::error('Error en AuditRecordController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al obtener los registros de auditoría.',
            ], 500);
        }
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
