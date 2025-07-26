<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon_path',
        'user_id'
    ];

    public function user()
    {
//        return $this->belongsToMany(User::class,'user_id')->withTimestamps()->withPivot('is_done','progress_percentage');
        return $this->belongsTo(User::class,'user_id');
    }
}
