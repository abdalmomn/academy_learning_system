<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
