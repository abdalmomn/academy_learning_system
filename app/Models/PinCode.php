<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinCode extends Model
{
    protected $fillable = [
        'user_id',
        'pin_code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
