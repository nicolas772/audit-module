<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class CourseAudit extends Model
{
    use BelongsToTenant;

    protected $table = 'course_audit';

    public $timestamps = false;

    protected $casts = [
        'diffs' => 'array',
        'created_at' => 'datetime',
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
}