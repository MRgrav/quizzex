<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_INSTITUTE = 'institute';
    public const ROLE_PARTICIPANT = 'participant';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_INSTITUTE,
        self::ROLE_PARTICIPANT,
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING = 'pending';
    public const STATUS_BLOCKED = 'blocked';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_PENDING,
        self::STATUS_BLOCKED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'institute_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the institute associated with the user (if user is an institute admin).
     */
    public function institute(): HasOne
    {
        return $this->hasOne(Institute::class);
    }

    /**
     * Get the institute that the participant belongs to.
     */
    public function participantInstitute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }

    /**
     * Get all quiz attempts made by this participant.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'participant_id');
    }
}
