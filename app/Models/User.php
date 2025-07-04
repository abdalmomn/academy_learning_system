<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
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

    public function academic_certificates(): hasMany
    {
        return $this->hasMany(AcademicCertificate::class);
    }
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class)->withTimestamps()->withPivot('is_done','progress_percentage');
    }

    public function banned_user()
    {
        return $this->hasOne(BannedUser::class);
    }

    public function isBanned()
    {
        return $this->banRecord !== null;
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function courses()
    {
        return $this->hasOne(Course::class);
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

    public function leader_board()
    {
        return $this->belongsTo(LeaderBoard::class, 'leader_id');
    }
}
