<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchLater extends Model
{
    protected $table = 'watch_later';
    protected $fillable = [
        'user_id',
        'video_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}
