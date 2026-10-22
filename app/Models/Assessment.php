<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'assessment_name',
        'marks_obtained',
        'max_marks',
        'assessment_date',
        'recorded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'integer',
            'max_marks' => 'integer',
            'assessment_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function getPercentageAttribute(): float
    {
        $max = $this->max_marks;
        if ($max <= 0) {
            return 0.0;
        }

        return round(($this->marks_obtained / $max) * 100, 2);
    }
}
