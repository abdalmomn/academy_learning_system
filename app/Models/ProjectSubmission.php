<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSubmission extends Model
{
    protected $fillable = [
        'file_path',
        'user_id',
        'question_id',
        'grade',
        'feedback'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
