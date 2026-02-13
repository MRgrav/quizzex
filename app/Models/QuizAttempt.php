<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ABANDONED = 'abandoned';

    public const STATUSES = [
        self::STATUS_IN_PROGRESS,
        self::STATUS_SUBMITTED,
        self::STATUS_ABANDONED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'participant_id',
        'quiz_id',
        'started_at',
        'submitted_at',
        'score',
        'total_possible_score',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
        'total_possible_score' => 'decimal:2',
    ];

    /**
     * Get the participant (user) who made this attempt.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * Get the quiz that was attempted.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get all answers for this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Check if the attempt is completed (submitted).
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED && $this->submitted_at !== null;
    }

    /**
     * Check if the attempt is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Calculate the percentage score.
     */
    public function getPercentageAttribute(): float
    {
        if ($this->total_possible_score == 0) {
            return 0;
        }

        return round(($this->score / $this->total_possible_score) * 100, 2);
    }

    /**
     * Check if the attempt has expired based on quiz duration.
     */
    public function isExpired(): bool
    {
        if (!$this->quiz || !$this->quiz->duration_minutes) {
            return false;
        }

        $expiresAt = $this->started_at->addMinutes($this->quiz->duration_minutes);
        return now()->isAfter($expiresAt);
    }

    /**
     * Get remaining time in seconds.
     * Returns 0 if expired or no duration set.
     */
    public function getRemainingTime(): int
    {
        if (!$this->quiz || !$this->quiz->duration_minutes) {
            return 0;
        }

        $expiresAt = $this->started_at->addMinutes($this->quiz->duration_minutes);
        $remainingSeconds = now()->diffInSeconds($expiresAt, false);

        return max(0, (int) $remainingSeconds);
    }

    /**
     * Check if the attempt can be submitted.
     * Must be in progress and not expired.
     */
    public function canSubmit(): bool
    {
        return $this->isInProgress() && !$this->isExpired();
    }
}
