<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{
    protected $table = 'user_attendance';
    protected $fillable = [
        'is_attendance',
        'user_id',
        'video_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
