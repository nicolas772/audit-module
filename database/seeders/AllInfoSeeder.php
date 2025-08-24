<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;

class AllInfoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los tenants existentes
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Setear el current tenant en el contexto de aplicación
            app()->instance('currentTenantId', $tenant->id);
            // Obtener el primer usuario del tenant
            $user = User::where('tenant_id', $tenant->id)->first();

            // Loguear al usuario para que Auditing funcione correctamente
            Auth::login($user);

            // Crear usuarios
            $users = User::factory()->count(2)->create([
                'tenant_id' => $tenant->id,
            ]);

            // Crear cursos
            $courses = Course::factory()->count(2)->create([
                'tenant_id' => $tenant->id,
            ]);

            foreach ($users as $user) {
                foreach ($courses as $course) {
                    CourseEnrollment::create([
                        'id' => Str::uuid(),
                        'tenant_id' => $tenant->id,
                        'user_id' => $user->uuid,
                        'course_id' => $course->id,
                        'enrolled_at' => now(),
                        'isCompleted' => fake()->boolean(),
                    ]);
                }
            }
        }
    }
}
