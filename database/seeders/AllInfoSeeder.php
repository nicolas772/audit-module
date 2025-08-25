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
        $auditRecordsQty = config('app.audit_records_count');
        
        // Obtener todos los tenants existentes
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Setear el current tenant en el contexto de aplicación
            app()->instance('currentTenantId', $tenant->id);
            // Obtener el primer usuario del tenant
            $adminUser = User::where('tenant_id', $tenant->id)->first();

            // Loguear al usuario para que Auditing funcione correctamente
            Auth::login($adminUser);

            // Crear Usuarios
            $users = User::factory()->count($auditRecordsQty)->create([
                'tenant_id' => $tenant->id,
            ]);

            // Generacion de distintos tipos de auditoría para users_audit
            foreach ($users as $user) {
                $user->update(['full_name' => $user->full_name . ' actualizado']);
                $user->delete();
            }

            // Crear Cursos
            $courses = Course::factory()->count($auditRecordsQty)->create([
                'tenant_id' => $tenant->id,
            ]);

            // Generacion de distintos tipos de auditoría para courses_audit
            foreach ($courses as $course) {
                $course->update(['title' => $course->title . ' actualizado']);
                $course->delete();
            }

            // Crear / Update / Delete Inscripciones
            foreach (range(1, $auditRecordsQty) as $_) {
                $user = User::factory()->create(['tenant_id' => $tenant->id]);
                $course = Course::factory()->create(['tenant_id' => $tenant->id]);

                $enrollment = CourseEnrollment::create([
                    'id' => Str::uuid(),
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->uuid,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                    'isCompleted' => fake()->boolean(),
                ]);

                // Generacion de distintos tipos de auditoría para course_enrollments_audit
                $enrollment->update(['isCompleted' => !$enrollment->isCompleted]);
                $enrollment->delete();
            }
        }
    }
}
