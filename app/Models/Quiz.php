<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'institute_id',
        'created_by',
        'status',
        'duration_minutes',
        'total_questions',
        'total_points',
        'start_time',
        'end_time',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the institute that owns the quiz (null for CSIR quizzes).
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the user who created the quiz.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all questions for this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Check if this is a CSIR quiz (no institute_id).
     */
    public function isCsirQuiz(): bool
    {
        return $this->institute_id === null;
    }

    /**
     * Check if quiz is visible to approved institutes and participants.
     */
    public function isVisible(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if quiz is currently live (within availability window).
     */
    public function isLive(): bool
    {
        if (!$this->start_time || !$this->end_time) {
            return false;
        }

        $now = now();
        return $now->between($this->start_time, $this->end_time);
    }

    /**
     * Check if quiz can be edited (not live and not manually locked).
     */
    public function canBeEdited(): bool
    {
        return !$this->isLive() && !$this->is_locked;
    }

    /**
     * Get all attempts made for this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
