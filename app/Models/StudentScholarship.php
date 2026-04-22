<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentScholarship extends Model
{
    protected $table = 'student_scholarships';

    protected $fillable = [
        'student_id',
        'scholarship_id',
        'discount_type', // 'percent' or 'amount'
        'discount_value',
        'original_amount',
        'discount_amount',
        'final_amount',
        'applied_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'applied_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id');
    }
}
