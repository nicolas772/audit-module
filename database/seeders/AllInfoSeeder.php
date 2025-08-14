<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\UserAudit;
use App\Models\CourseAudit;
use App\Models\CourseEnrollmentAudit;
use App\Enums\AuditActionType;

class AllInfoSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            'Buk',
            'Amazon',
        ];

        foreach ($tenants as $name) {
            $tenant = Tenant::create([
                'id' => Str::uuid(),
                'name' => $name,
            ]);

            // app()->instance('currentTenantId', $tenant->id);

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
                    $enrollment = CourseEnrollment::create([
                        'id' => Str::uuid(),
                        'tenant_id' => $tenant->id,
                        'user_id' => $user->uuid,
                        'course_id' => $course->id,
                        'enrolled_at' => now(),
                        'isCompleted' => fake()->boolean(),
                    ]);

                    // Simular auditoría
                    CourseEnrollmentAudit::create([
                        'tenant_id' => $tenant->id,
                        'object_id' => $enrollment->id,
                        'type' => AuditActionType::Create,
                        'diffs' => [
                            'isCompleted' => [null, $enrollment->isCompleted],
                        ],
                        'transaction_hash' => Str::uuid(),
                        'blame_id' => $user->uuid,
                        'blame_user' => $user->full_name,
                        'created_at' => now(),
                    ]);
                }

                // Simular auditoría de usuario
                UserAudit::create([
                    'tenant_id' => $tenant->id,
                    'object_id' => $user->uuid,
                    'type' => AuditActionType::Update,
                    'diffs' => [
                        'full_name' => [$user->full_name, $user->full_name . ' Updated'],
                    ],
                    'transaction_hash' => Str::uuid(),
                    'blame_id' => $user->uuid,
                    'blame_user' => $user->full_name,
                    'created_at' => now(),
                ]);
            }

            // Simular auditoría de curso
            foreach ($courses as $course) {
                CourseAudit::create([
                    'tenant_id' => $tenant->id,
                    'object_id' => $course->id,
                    'type' => AuditActionType::Delete,
                    'diffs' => [
                        'title' => [null, $course->title],
                    ],
                    'transaction_hash' => Str::uuid(),
                    'blame_id' => $users[0]->uuid,
                    'blame_user' => $users[0]->full_name,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
