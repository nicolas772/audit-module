<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model implements AuditableContract
{
    use BelongsToTenant, HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'courses';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $auditInclude = [
        'id',
        'tenant_id',
        'title',
        'description',
    ];

    protected $fillable = [
        'id',
        'tenant_id',
        'title',
        'description',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id', 'id');
    }
}
