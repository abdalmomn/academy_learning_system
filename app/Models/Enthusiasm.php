<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enthusiasm extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'watch_time',
        'attended',
        'streak'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
