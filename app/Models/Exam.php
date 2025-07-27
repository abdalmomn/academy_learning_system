<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'type',
        'exam_mode',
        'title',
        'description',
        'start_date',
        'end_date',
        'duration_minutes',
        'is_mandatory',
        'course_id',
        'video_id'
    ];
    public function course()
    {
        return $this->belongsTo(Course::class,'course_id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class,'video_id');
    }
//    public function exam_results()
    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
