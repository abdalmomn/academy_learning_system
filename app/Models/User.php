<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'role',
        'email',
        'password',
        'social_id',
        'social_type',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function academic_certificates(): hasMany
    {
        return $this->hasMany(AcademicCertificate::class, 'teacher_id', 'id');
    }
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class)->withTimestamps()->withPivot('is_done', 'progress_percentage');
    }
//    public function academic_certificates()
//    {
//        return $this->hasMany(AcademicCertificate::class);
//    }
//    public function achievements()
//    {
//        return $this->hasMany(Achievement::class);
//    }

    public function banned_user()
    {
        return $this->hasOne(BannedUser::class);
    }

    public function isBanned()
    {
        return $this->banRecord !== null;
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withPivot(['is_completed', 'completed_at', 'certificate_id'])
            ->withTimestamps();
    }

    public function courseRate()
    {
        return $this->hasMany(CourseRating::class);
    }
    public function teacher_ratings_given()
    {
        return $this->hasMany(TeacherRating::class, 'user_id');
    }



    // Ratings this user (as a teacher) received
    public function teacher_ratings_received()
    {
        return $this->hasMany(TeacherRating::class, 'teacher_id');
    }

    public function strike()
    {
        return $this->hasOne(Strike::class);
    }
    public function teacherRate()
    {
        return $this->hasMany(TeacherRating::class);
    }

    public function enthusiasm()
    {
        return $this->hasOne(Enthusiasm::class);
    }
    public function examResult()
    {
        return $this->hasOne(ExamResult::class);
    }
    public function interested_categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }
    public function interests()
    {
        return $this->hasMany(Interest::class);
    }

    public function mcqAnswers()
    {
        return $this->hasMany(McqAnswer::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function profile_details()
    {
        return $this->hasOne(ProfileDetail::class);
    }
    public function project()
    {
        return $this->hasOne(ProjectSubmission::class);
    }
    public function promoCode()
    {
        return $this->hasMany(PromoCode::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function watch_later()
    {
        return $this->belongsToMany(Video::class, 'watch_later')->withTimestamps();
    }

    public function leader_board()
    {
        return $this->belongsTo(LeaderBoard::class, 'leader_id');
    }
    public function certificates()
    {
        return $this->hasManyThrough(Certificate::class, 'course_user');
    }
    public function attendedVideos()
    {
        return $this->belongsToMany(Video::class, 'user_attendance')
            ->withPivot('is_attendance')
            ->withTimestamps();
    }


    public function watchLater()
    {
        return $this->hasMany(WatchLater::class);
    }
}
