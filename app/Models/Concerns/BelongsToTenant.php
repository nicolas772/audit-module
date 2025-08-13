<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($tenantId = app('currentTenantId')) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId); #Filtra automaticamente por tenant_id todas las consultas
        }
    }
}

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        // Agrega el tenant_id automáticamente al crear si no se pasó manualmente.
        static::creating(function (Model $model) {
            if (!$model->tenant_id && ($tenantId = app('currentTenantId'))) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}
