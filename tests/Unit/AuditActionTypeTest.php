<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Enums\AuditActionType;
use PHPUnit\Framework\Attributes\Test;
use InvalidArgumentException;

class AuditActionTypeTest extends TestCase
{
    /**
     * Test a método label.
     */
    public function test_it_returns_correct_label_for_each_case()
    {
        $this->assertSame('Created', AuditActionType::Created->label());
        $this->assertSame('Updated', AuditActionType::Updated->label());
        $this->assertSame('Deleted', AuditActionType::Deleted->label());
    }

    /**
     * Test a método fromName.
     * Se prueban distintas opciones por strlower() 
     */
    public function test_it_resolves_enum_from_name()
    {
        $this->assertSame(AuditActionType::Created, AuditActionType::fromName('created'));
        $this->assertSame(AuditActionType::Updated, AuditActionType::fromName('Updated'));
        $this->assertSame(AuditActionType::Deleted, AuditActionType::fromName('DELETED'));
    }

    /**
     * Test error a método fromName.
     * La diferencia con tryFromName es que esta función devuelve un invalid argument exception para el caso default
     */
    public function test_it_throws_exception_if_invalid_name_is_provided_to_fromName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid audit action type: unknown_action_type');

        AuditActionType::fromName('unknown_action_type');
    }

    /**
     * Test error a método tryFromName.
     * La diferencia con fromName es que esta función devuelve null para el caso default
     */
    public function test_it_returns_null_if_invalid_name_is_provided_to_tryFromName()
    {
        $this->assertNull(AuditActionType::tryFromName('foo'));
    }

    /**
     * Test  a método validNames.
     */
    public function test_it_returns_valid_names()
    {
        $valid = AuditActionType::validNames();

        $this->assertEqualsCanonicalizing(['created', 'updated', 'deleted'], $valid);
    }
}
