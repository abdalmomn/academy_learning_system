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
<<<<<<< HEAD
        return $this->hasMany(User::class);
    }

//    public function course()
//    {
//        return $this->belongsTo(Course::class, 'course_id');
//    }
=======
        return $this->morphTo();
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
>>>>>>> ca7ced0 (first version: database, models and spatie role)
}
