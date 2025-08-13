<?php

namespace App\Enums;

enum AuditActionType: int
{
    case Create = 1;
    case Update = 2;
    case Delete = 3;

    public function label(): string
    {
        return match ($this) {
            self::Create => 'Create',
            self::Update => 'Update',
            self::Delete => 'Delete',
        };
    }

    public static function fromName(string $name): ?self
    {
        return match (strtolower($name)) {
            'create' => self::Create,
            'update' => self::Update,
            'delete' => self::Delete,
            default => null,
        };
    }
}