<?php

namespace App\Enums;

enum AuditActionType: int
{
    case Created = 1;
    case Updated = 2;
    case Deleted = 3;

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Updated => 'Updated',
            self::Deleted => 'Deleted',
            default => throw new \InvalidArgumentException("Invalid audit action label: $this"),
        };
    }

    public static function fromName(string $name): ?self
    {
        return match (strtolower($name)) {
            'created' => self::Created,
            'updated' => self::Updated,
            'deleted' => self::Deleted,
            default => throw new \InvalidArgumentException("Invalid audit action type: $name"),
        };
    }

    public static function validNames(): array
    {
        $validActions = array_map(
            fn(self $case) => strtolower($case->label()),
            self::cases()
        );
        return $validActions;
    }
}