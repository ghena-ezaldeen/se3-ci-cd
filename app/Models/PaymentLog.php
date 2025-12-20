<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Payment Log Model
|--------------------------------------------------------------------------
| Stores raw records from payment gateway interactions for traceability.
| It keeps references to transactions plus gateway metadata so later
| adapters and reconciliations can audit external payment flows.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'transaction_id',
        'gateway',
        'reference',
        'amount',
        'currency',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}

