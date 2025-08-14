<?php

namespace App\Auditing\Resolvers;

class TenantResolver
{
    public static function resolve(): ?string
    {
        // El middleware SetCurrentTenant agrego: app()->instance('currentTenantId', $tenantId)
        return app()->bound('currentTenantId') ? app('currentTenantId') : null;
    }
}
