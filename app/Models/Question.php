<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TRUE_FALSE = 'true_false';
    // public const TYPE_SHORT_ANSWER = 'short_answer';

    public const TYPES = [
        self::TYPE_MULTIPLE_CHOICE,
        self::TYPE_TRUE_FALSE,
        // self::TYPE_SHORT_ANSWER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'points',
        'order',
        'explanation',
    ];

    /**
     * Get the quiz that owns this question.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get all options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order');
    }

    /**
     * Get the correct option(s) for this question.
     */
    public function correctOptions(): HasMany
    {
        return $this->hasMany(Option::class)->where('is_correct', true);
    }

    /**
     * Get all answers for this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
