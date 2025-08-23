<?php

namespace App\Models\Audit;

use App\Models\UserAudit;
use App\Models\CourseAudit;
use App\Models\CourseEnrollmentAudit;

class AuditTableMap
{
    public static function resolve(string $table): ?string
    {
        return match ($table) {
            'users_audit' => UserAudit::class,
            'courses_audit' => CourseAudit::class,
            'course_enrollments_audit' => CourseEnrollmentAudit::class,
            default => null,
        };
    }
}
