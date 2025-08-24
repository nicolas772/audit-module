<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;
use App\Enums\AuditActionType;

class CourseEnrollmentAudit extends Model
{
    use BelongsToTenant;

    protected $table = 'course_enrollments_audit';

    public $timestamps = false;

    protected $casts = [
        'diffs' => 'array',
        'created_at' => 'datetime',
        'type' => AuditActionType::class,
    ];

    protected $fillable = [
        'tenant_id',
        'object_id',
        'type',
        'diffs',
        'transaction_hash',
        'blame_id',
        'blame_user',
        'created_at',
    ];

    public function scopeType($query, array $types): void
    {
        $enums = collect($types)
            ->map(fn($type) => AuditActionType::fromName($type))
            ->filter()
            ->values();

        $query->whereIn('type', $enums);
    }

    public function scopeObjectId($query, string $objectId): void
    {
        $query->where('object_id', $objectId);
    }

    public function scopeFromDate($query, string $startDate): void
    {
        $query->whereDate('created_at', '>=', $startDate);
    }

    public function scopeToDate($query, string $endDate): void
    {
        $query->whereDate('created_at', '<=', $endDate);
    }
}