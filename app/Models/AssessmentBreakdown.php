<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentBreakdown extends PrefixedModel
{
    protected $table = 'assessment_breakdown';

    protected $fillable = [
        'assessment_id',
        'source_type',
        'source_id',
        'description',
        'units',
        'rate',
        'amount',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessments::class, 'assessment_id');
    }

    protected function idPrefix(): string
    {
        return 'ABD';
    }

    protected function casts(): array
    {
        return [
            'units' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }
}
