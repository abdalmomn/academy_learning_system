<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $fillable = [
        'email',
        'reset_code',
        'token',
        'code_expires_at',
        'token_expires_at'
    ];
}
