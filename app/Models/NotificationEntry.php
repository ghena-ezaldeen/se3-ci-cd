<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Notification Entry Model
|--------------------------------------------------------------------------
| Represents user-facing notifications stored in the database. It sits
| alongside Laravel's native notifications but stays tailored to banking
| events (approvals, scheduled runs, tickets) without mixing with system
| framework types.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationEntry extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'notifications';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'channel',
        'message',
        'status',
        'notifiable_type',
        'notifiable_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}

