<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'course_id',
        'description',
        'duration',
        'url',
        'title',
        'poster'
    ];
    public function exam()
    {
        return $this->hasOne(Exam::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class,'course_id');
    }
    public function users_who_watched_later()
    {
        return $this->belongsToMany(User::class, 'user_video')->withTimestamps();
    }
    public function attendedUsers()
    {
        return $this->belongsToMany(User::class, 'user_attendance')
            ->withPivot('is_attendance')
            ->withTimestamps();
    }
}
