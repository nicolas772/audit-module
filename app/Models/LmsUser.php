<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class LmsUser extends Model
{
    use BelongsToTenant;

    protected $table = 'users'; // usa la misma tabla que User

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'email',
        'full_name',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'user_id', 'uuid');
    }
}
