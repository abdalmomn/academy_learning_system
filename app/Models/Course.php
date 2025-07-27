<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_name',
        'description',
        'rating',
        'status',
        'is_paid',
        'start_date',
        'end_date',
        'user_id',
        'category_id'
    ];

//    public function users()
//    {
//        return $this->belongsToMany(User::class)
//            ->withPivot(['is_completed' , 'certificate_id'])
//            ->withTimestamps();
    public function user()
    {
        return $this->belongsToMany(User::class,'user_courses')
            ->withPivot(['is_completed' , 'certificate_id'])
            ->withTimestamps();
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function rate()
    {
        return $this->hasMany(CourseRating::class);
    }
//    public function course_requirements()
    public function requirements()
    {
        return $this->hasMany(CourseRequirement::class);
    }
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
