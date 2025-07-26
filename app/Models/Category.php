<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;


    protected $fillable = [
        'category_name'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
//    public function interested_users()
//    {
//        return $this->belongsToMany(User::class)->withTimestamps();
    public function interests()
    {
        return $this->hasMany(Interest::class);
    }
}
