<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class Course extends Model
{
    use BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';

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
        return $this->hasMany(CourseEnrollment::class);
    }
}
