<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Audit\AuditTableMap;

class AuditTableMapTest extends TestCase
{
    /**
     * Test a metodo All.
     */
    public function test_it_returns_all_audit_table_names()
    {
        $tables = AuditTableMap::all();

        $this->assertIsArray($tables);
        $this->assertContains('users_audit', $tables);
        $this->assertContains('courses_audit', $tables);
        $this->assertContains('course_enrollments_audit', $tables);
    }

    public function test_it_resolves_correct_model_class_from_table_name()
    {
        $this->assertEquals(
            \App\Models\UserAudit::class,
            AuditTableMap::resolve('users_audit')
        );

        $this->assertEquals(
            \App\Models\CourseAudit::class,
            AuditTableMap::resolve('courses_audit')
        );

        $this->assertEquals(
            \App\Models\CourseEnrollmentAudit::class,
            AuditTableMap::resolve('course_enrollments_audit')
        );
    }

    public function test_it_returns_null_for_unknown_table()
    {
        $this->assertNull(
            AuditTableMap::resolve('unknown_table_name')
        );
    }
}
