<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teachers extends PrefixedModel
{
    protected $table = 'teachers';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'teacher_id',
        'first_name',
        'middle_name',
        'last_name',
        'department',
        'status',
    ];

    protected function idPrefix(): string
    {
        return 'TCH';
    }

    /**
     * Get the user associated with this teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subjectAssignments(): HasMany
    {
        return $this->hasMany(SubjectTeacherAssignment::class, 'teacher_id');
    }

    /**
     * Get full name of teacher.
     */
    public function getFullNameAttribute(): string
    {
        $full = $this->first_name;
        if ($this->middle_name) {
            $full .= ' '.$this->middle_name;
        }
        $full .= ' '.$this->last_name;

        return $full;
    }

    /**
     * Get email from associated user.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    /**
     * Disable HasPrefixedId trait's ID generation
     */
    protected static function bootHasPrefixedId(): void
    {
        // Override to disable automatic ID generation
    }
}
