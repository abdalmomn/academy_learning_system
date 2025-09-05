<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'faq_category_id'
    ];
    public function faq_category()
    {
        return $this->belongsTo(Faq_categories::class,'faq_category_id');
    }
}
