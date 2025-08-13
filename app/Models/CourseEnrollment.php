<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class CourseEnrollment extends Model
{
    use BelongsToTenant;

    protected $table = 'course_enrollments';

    public $incrementing = false;
    protected $keyType = 'string';

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
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
