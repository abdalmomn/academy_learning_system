<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderBoard extends Model
{
    protected $fillable = [
        'leader_id',
        'leader_type',
        'points'
    ];

    public function leader()
    {
        return $this->hasMany(User::class);
    }

//    public function course()
//    {
//        return $this->belongsTo(Course::class, 'course_id');
//    }
}
