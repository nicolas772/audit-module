<?php

namespace App\Auditing\Drivers;

use App\Enums\AuditActionType;
use App\Auditing\Resolvers\TenantResolver;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\AuditDriver;
use OwenIt\Auditing\Models\Audit as CoreAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PerTableDatabaseDriver implements AuditDriver
{
    public function audit(Auditable $model): Audit
    {
        $eloquent = $model;
        
        // Tabla destino: <tabla_modelo>_audit
        $modelTable = $eloquent->getTable(); // esto NO debe ser null
        if (empty($modelTable)) {
            throw new \RuntimeException(
                'No se pudo determinar la tabla base para auditoría en: '.get_class($eloquent)
            );
        }
        $table = $modelTable.'_audit';

        // Verifica que la tabla exista
        if (!Schema::hasTable($table)) {
            throw new \RuntimeException("La tabla de auditoría [$table] no existe en la BD.");
        }
        
        // Datos que prepara el trait Auditable
        $data  = $model->toAudit();
        $event = $data['event'] ?? 'updated';
        
        // Mapeo a enum smallint
        $type = AuditActionType::fromName($event);

        // Diffs: {"campo":[old,new]}. Solo guarda los campos que modificados
        $old = $data['old_values'] ?? [];
        $new = $data['new_values'] ?? [];
        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $diffs = [];
        foreach ($keys as $k) {
            $o = Arr::get($old, $k);
            $n = Arr::get($new, $k);
            if ($o !== $n) {
                $diffs[$k] = [$o, $n];
            }
        }

        // Identificador del objeto por ID (todos tus modelos lo tienen, excepto users que utiliza uuid)
        $attribute = $modelTable == 'users' ? 'uuid' : 'id';
        $objectUuid = $eloquent->getAttribute($attribute);

        // Contexto
        $tenantId  = TenantResolver::resolve();
        $blameId   = Auth::user()->uuid;
        $blameUser = Auth::user()->full_name;

        // Un hash transaccional por request. Para operaciones con tinker, usa auditable_id ya que no hay request hhtp
        $tx = request()->attributes->get('tx_hash') ?? $data['auditable_id'];

        // Inserción
        DB::table($table)->insert([
            'tenant_id'        => $tenantId,
            'object_id'        => $objectUuid,
            'type'             => $type,
            'diffs'            => json_encode($diffs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'transaction_hash' => $tx,
            'blame_id'         => $blameId,
            'blame_user'       => $blameUser,
            'created_at'       => now(),
        ]);

        // Devolver una instancia de Audit del paquete para cumplir la firma
        return new CoreAudit([
            'auditable_type' => $eloquent->getMorphClass(),
            'auditable_id'   => $eloquent->getKey(),
            'event'          => $event,
            'old_values'     => $old,
            'new_values'     => $new,
            'user_id'        => $blameId,
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
