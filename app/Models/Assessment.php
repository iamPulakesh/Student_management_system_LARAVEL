<?php

/**
 * Model for a student's assessment mark in a course.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    /**
     * How many months back an assessment can be dated.
     */
    public const RECORDING_WINDOW_MONTHS = 6;

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

    /**
     * The earliest date (Y-m-d) an assessment can be dated.
     */
    public static function earliestRecordableDate(): string
    {
        return now()->subMonths(self::RECORDING_WINDOW_MONTHS)->toDateString();
    }

    /**
     * The maximum marks already used for this assessment in the course,
     * or null if no mark has been recorded for it yet.
     */
    public static function maxMarksInUse(int $courseId, string $assessmentName, ?int $ignoreAssessmentId = null): ?int
    {
        $maxMarks = self::query()
            ->where('course_id', $courseId)
            ->where('assessment_name', $assessmentName)
            ->when($ignoreAssessmentId, fn ($query) => $query->whereKeyNot($ignoreAssessmentId))
            ->value('max_marks');

        return $maxMarks === null ? null : (int) $maxMarks;
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

    public function getPercentageAttribute(): float
    {
        $max = $this->max_marks;
        if ($max <= 0) {
            return 0.0;
        }

        return round(($this->marks_obtained / $max) * 100, 2);
    }
}
