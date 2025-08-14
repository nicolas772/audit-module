<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\BelongsToTenant;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /* Lo siguiente es para setear uuid como PK, pero daña Auth. De todas maneras, se sigue utilizando uuid para las relaciones
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';*/

    protected $table = 'users';

    protected $auditInclude = [
        'id',
        'uuid',
        'tenant_id',
        'full_name',
        'email',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'tenant_id',
        'full_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'user_id', 'uuid');
    }
}
