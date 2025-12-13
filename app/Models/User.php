<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| User Model
|--------------------------------------------------------------------------
| This model represents application users across the banking system. It
| exists to encapsulate authentication data and connect users to domain
| entities such as accounts, transactions, roles, tickets, and alerts.
| Relationships defined here allow later modules (RBAC, approvals, etc.)
| to hook into a consistent user graph without duplicating logic.
*/

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function initiatedTransactions()
    {
        return $this->hasMany(Transaction::class, 'initiated_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationEntry::class, 'user_id');
    }
}
