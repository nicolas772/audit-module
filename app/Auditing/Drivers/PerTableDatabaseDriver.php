<?php

namespace App\Auditing\Drivers;

use App\Enums\AuditActionType;
use App\Auditing\Resolvers\TenantResolver;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\AuditDriver;
use OwenIt\Auditing\Models\Audit as CoreAudit;
use Illuminate\Support\Facades\Auth;
use App\Models\Audit\AuditTableMap;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Schema;

class PerTableDatabaseDriver implements AuditDriver
{
    public function audit(Auditable $model): Audit
    {
        try {
            $auditModel = $this->resolveAuditModel($model);
            $data       = $model->toAudit();
            $type       = AuditActionType::fromName($data['event'] ?? '');
            $diffs      = $this->extractDiffs($data);
            $context    = $this->createAuditContext($model, $data);

            // Se inserta utilizando modelo
            $auditModel->create(array_merge($context, [
                'type'  => $type,
                'diffs' => $diffs,
            ]));

            // El contrato de Audit Driver indica que se debe retornar un CoreAudit
            return $this->createCoreAudit($model, $data);
        } catch (\Throwable $e) {
            Log::error('[Audit] Error al guardar auditoría', [
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);

            // En vez de throw error, retorno objeto para que falle silenciosamente.
            // Aqui supone un compromiso de revisar periodicamente log de errores
            return new CoreAudit();
        }
    }

    protected function resolveAuditModel(Auditable $model): \Illuminate\Database\Eloquent\Model
    {
        // Tabla destino: <tabla_modelo>_audit
        $table = $model->getTable(); // esto NO debe ser null
        if (! $table) {
            throw new \RuntimeException("No se pudo determinar la tabla base para auditoría en: " . get_class($model));
        }

        // Utiliza el metodo resolve para obtener el modelo a partir del nombre de la tabla
        $auditModelClass = AuditTableMap::resolve($table . '_audit');

        // Verifica que el modelo de tabla de auditoria exista
        if (! $auditModelClass || ! class_exists($auditModelClass)) {
            throw new \RuntimeException("No se pudo resolver el modelo de auditoría para [$table]");
        }

        return new $auditModelClass;
    }

    protected function extractDiffs(array $data): array
    {
        // Diferencias en campos
        return [
            'old_values' => $data['old_values'] ?? [],
            'new_values' => $data['new_values'] ?? [],
        ];
    }

    protected function createAuditContext(Auditable $model, array $data): array
    {
        $table = $model->getTable();
        if (empty($table)) {
            throw new \RuntimeException(
                'No se pudo determinar la tabla base para auditoría en: ' . get_class($model)
            );
        }

        $attribute = $table === 'users' ? 'uuid' : 'id';
        $objectId  = $model->getAttribute($attribute);
        $tenantId  = TenantResolver::resolve();
        $user      = Auth::user();

        // Un hash transaccional por request. Para operaciones con tinker, se crea un tx aleatorio
        $tx = request()->attributes->get('tx_hash') ?? 'seed-'.Str::random(8);

        return [
            'tenant_id'        => $tenantId,
            'object_id'        => $objectId,
            'transaction_hash' => $tx,
            'blame_id'         => $user?->uuid,
            'blame_user'       => $user?->full_name,
            'created_at'       => now(),
        ];
    }

    protected function createCoreAudit(Auditable $model, array $data): CoreAudit
    {
        return new CoreAudit([
            'auditable_type' => $model->getMorphClass(),
            'auditable_id'   => $model->getKey(),
            'event'          => $data['event'] ?? 'updated',
            'old_values'     => $data['old_values'] ?? [],
            'new_values'     => $data['new_values'] ?? [],
            'user_id'        => Auth::user()?->uuid,
            'url'            => request()->fullUrl() ?: null,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'tags'           => null,
        ]);
    }

    /**
     * Remove older audits that go over the threshold.
     *
     * @param \OwenIt\Auditing\Contracts\Auditable $model
     *
     * @return bool
     */
    public function prune(Auditable $model): bool
    {
        // TODO: Esta lógica debe implementar el borrado de auditorias antiguas dado un threshold
        return true;
    }
}
