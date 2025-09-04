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
    public function options()
    {
        return $this->hasMany(McqOption::class);
    }
    public function projects()
    {
        return $this->hasMany(ProjectSubmission::class);
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class,'exam_id');
    }
}
