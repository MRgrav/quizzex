<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institute extends Model
{
    use HasFactory;

    public const TYPE_SCHOOL = 'school';
    public const TYPE_COLLEGE = 'college';

    public const TYPES = [
        self::TYPE_SCHOOL,
        self::TYPE_COLLEGE,
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'address',
        'contact_person',
        'phone',
        'user_id',
        'status',
    ];

    /**
     * Get the user (institute admin) that owns the institute.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all participants belonging to this institute.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(User::class, 'institute_id')->where('role', User::ROLE_PARTICIPANT);
    }
}
