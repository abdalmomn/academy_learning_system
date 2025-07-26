<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $fillable = [
        'email',
<<<<<<< HEAD
        'reset_code',
        'token',
        'code_expires_at',
        'token_expires_at'
=======
        'code'
>>>>>>> ca7ced0 (first version: database, models and spatie role)
    ];
}
