<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnrollment extends Model implements AuditableContract
{
    use BelongsToTenant, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'course_enrollments';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $auditInclude = [
        'id',
        'tenant_id',
        'user_id',
        'course_id',
        'enrolled_at',
        'isCompleted',
    ];

    protected $fillable = [
        'id',
        'tenant_id',
        'user_id',
        'course_id',
        'enrolled_at',
        'isCompleted',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'isCompleted' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
