<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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

    public function academic_certificates()
    {
        return $this->hasMany(AcademicCertificate::class);
    }
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

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
        return $this->hasMany(Course::class);
    }

    public function courseRate()
    {
        return $this->hasMany(CourseRating::class);
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
    public function details()
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
    public function watchLater()
    {
        return $this->hasMany(WatchLater::class);
    }
}
