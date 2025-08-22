<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\User;

class TenantAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenantNames = ['Buk', 'Amazon'];

        foreach ($tenantNames as $name) {
            $tenant = Tenant::create([
                'id' => Str::uuid(),
                'name' => $name,
            ]);

            User::withoutAuditing( function () use ($tenant) {
                User::create([
                    'uuid'      => Str::uuid(),
                    'tenant_id' => $tenant->id,
                    'full_name' => 'QA Auditor ' . $tenant->name,
                    'email'     => $tenant->name . '@QA.test',
                    'password'  => 'secret-123',
                ]);
            });
        }
    }
}
