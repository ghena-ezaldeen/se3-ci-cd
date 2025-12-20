<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Role Model
|--------------------------------------------------------------------------
| Represents a system role used for banking permissions (customer, teller,
| manager, admin). It exists to keep RBAC data normalized and to provide a
| clean pivot to users, so other modules (approvals, ticket triage, etc.)
| can quickly query user capabilities.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}

