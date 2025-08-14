<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            'Buk',
            'Amazon',
        ];

        foreach ($tenants as $name) {
            Tenant::create([
                'id' => Str::uuid(),
                'name' => $name,
            ]);
        }
    }
}
