<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'teacher_id',
        'promo_code',
        'discount_percentage',
        'usage_limit',
        'expires_in'
    ];
    public function teacher()
    {
        return $this->belongsTo(User::class,'teacher_id');
    }
}
