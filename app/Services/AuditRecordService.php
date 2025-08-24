<?php

namespace App\Services;

use App\Http\Requests\AuditRecordRequest;
use App\Models\Audit\AuditTableMap;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AuditRecordService
{
    public function getAuditRecords(AuditRecordRequest $request): LengthAwarePaginator
    {
        // Variables de Request
        $tableNames = $request->input('tables', []);
        $types      = $request->input('types', []);
        $objectId   = $request->input('object_id');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date'); 
        
        // Variables para paginación con LengthAwarePaginator
        $perPage    = $request->input('per_page', 4);
        $page       = LengthAwarePaginator::resolveCurrentPage();

        // Validación para entidades a filtrar
        if (!is_array($tableNames) || empty($tableNames)) {
            throw new \InvalidArgumentException('Debe especificar al menos una tabla de auditoría');
        }

        // Inicialización de estructura que almacena todos los registros consolidados
        $allRecords = collect();

        foreach ($tableNames as $table) {
            $records = $this->getRecordsFromTable($table, $types, $objectId, $startDate, $endDate);

            // Agregamos campo audit_table para identificar la tabla de origen por registros
            $records->each(fn($record) => $record->audit_table = $table);

            $allRecords = $allRecords->concat($records);
        }

        // Ordenamos todos los registros por fecha de creación
        $sorted = $allRecords->sortByDesc('created_at')->values();

        // Se retorna resultado ordenado y paginado
        return new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Recupera los registros de auditoría de una tabla específica.
     */
    protected function getRecordsFromTable(
        string $table,
        ?array $types,
        ?string $objectId,
        ?string $startDate,
        ?string $endDate
    ): Collection {
        // Clase que almacena las tablas auditables y sus respectivos modelos
        $modelClass = AuditTableMap::resolve($table);

        // Validación existencia de modelo
        if (!$modelClass || !class_exists($modelClass)) {
            Log::warning("Tabla no válida: $table");
            return collect();
        }

        // Obtención y retorno de datos a través de Eloquent, con filtros incluidos por scope
        return $modelClass::query()
            ->when($types && is_array($types), fn($q) => $q->type($types))
            ->when($objectId, fn($q) => $q->objectId($objectId))
            ->when($startDate, fn($q) => $q->fromDate($startDate))
            ->when($endDate, fn($q) => $q->toDate($endDate))
            ->get();
    }
}
