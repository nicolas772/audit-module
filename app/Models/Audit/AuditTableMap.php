<?php

namespace App\Models\Audit;

use App\Models\UserAudit;
use App\Models\CourseAudit;
use App\Models\CourseEnrollmentAudit;

class AuditTableMap
{
    public const AUDIT_TABLES = [
        'users_audit' => UserAudit::class,
        'courses_audit' => CourseAudit::class,
        'course_enrollments_audit' => CourseEnrollmentAudit::class,
    ];

    public static function all(): array
    {
        return array_keys(self::AUDIT_TABLES);
    }

    public static function resolve(string $table): ?string
    {
        return self::AUDIT_TABLES[$table] ?? null;
    }
}
