<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTeacherAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'days',
        'start_time',
        'end_time',
        'room',
        'status',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teachers::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    protected function casts(): array
    {
        return [
            'days' => 'array',
        ];
    }
}
