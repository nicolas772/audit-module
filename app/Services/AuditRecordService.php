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

            // Obtención de datos a través de Eloquent, con filtros incluidos por scope
            $records = $modelClass::query()
                ->when($request->filled('type'), fn($q) => $q->type($request->type))
                ->when($request->filled('object_id'), fn($q) => $q->objectId($request->object_id))#
                ->when($request->filled('start_date'), fn($q) => $q->fromDate($request->start_date))
                ->when($request->filled('end_date'), fn($q) => $q->toDate($request->end_date))
                ->get();

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
