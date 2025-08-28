<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'description',
        'transaction_method',
        'transaction_type'
    ];
    public function wallet()
    {
        return $this->belongsTo(Wallet::class,'wallet_id');
    }
}
