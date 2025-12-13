<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Account Feature Model
|--------------------------------------------------------------------------
| Captures feature decorators applied to accounts (e.g., overdraft, cash
| back). It keeps metadata about the decorator so the domain layer can add
| or remove capabilities without changing the core account record.
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountFeature extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'account_id',
        'feature_name',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

