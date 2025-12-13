<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Ticket Model
|--------------------------------------------------------------------------
| Stores support requests raised by users. It exists to give staff and
| workflows a place to track status, priority, and ownership as the
| banking platform scales its customer support capabilities.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status',
        'priority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

