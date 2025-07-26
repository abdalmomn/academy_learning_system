<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'question_text',
        'question_type',
        'mark'
    ];
<<<<<<< HEAD
//    public function answer()
//    {
//        return $this->hasOne(McqAnswer::class);
//    }
=======
    public function answers()
    {
        return $this->hasOne(McqAnswer::class);
    }
>>>>>>> ca7ced0 (first version: database, models and spatie role)
    public function projects()
    {
        return $this->hasMany(ProjectSubmission::class);
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class,'exam_id');
    }
}
