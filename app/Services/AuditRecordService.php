<?php

namespace App\Services;

use App\Enums\AuditActionType;
use App\Http\Requests\AuditRecordRequest;
use App\Models\Audit\AuditTableMap;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditRecordService
{
    public function getAuditRecords(AuditRecordRequest $request): LengthAwarePaginator
    {
        // Las entidades seleccionadas llegarán desde front como table[] = *_audit
        $tableNames = $request->input('tables', []);

        if (!is_array($tableNames) || empty($tableNames)) {
            throw new \InvalidArgumentException('Debe especificar al menos una tabla de auditoría');
        }

        // Inicialización de estructura que almacena resultados
        $allRecords = collect();

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

            // Obtenemos todos los resultados
            $records = $query->get();

            // Agregamos campo audit_table para identificar la tabla de origen por registros
            $records->transform(function ($item) use ($table) {
                $item->audit_table = $table;
                return $item;
            });

            $allRecords = $allRecords->concat($records);
        }

        // Ordenamos todos los registros por fecha de creación
        $sorted = $allRecords->sortByDesc('created_at')->values();

        // Paginación manual (nueva colección debido al requisito de tablas seleccionables)
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $request->input('per_page', 15);

        return new LengthAwarePaginator(
            $sorted->forPage($page, $perPage),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
