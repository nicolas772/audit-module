<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class LmsUserAudit extends Model
{
    use BelongsToTenant;

    protected $table = 'user_audit';

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