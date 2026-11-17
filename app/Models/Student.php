<?php

/**
 * Model for a student, enrolled in every course of their department and semester.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /**
     * Gender options offered on every student form.
     *
     * @var list<string>
     */
    public const GENDERS = ['Male', 'Female', 'Other'];

    /**
     * Youngest and oldest age accepted for a date of birth.
     */
    public const MIN_AGE = 15;

    public const MAX_AGE = 60;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'department_id',
        'user_id',
        'year_of_study',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'year_of_study' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Whether attendance or marks have been recorded for this student.
     */
    public function hasAcademicRecords(): bool
    {
        return $this->attendances()->exists() || $this->assessments()->exists();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Limit the query to students enrolled in the course: same department and
     * the student's semester matches the course's semester.
     */
    public function scopeEnrolledIn(Builder $query, Course $course): Builder
    {
        return $query->where('department_id', $course->department_id)
            ->where('year_of_study', $course->semester);
    }

    public function isEnrolledIn(Course $course): bool
    {
        return (int) $this->department_id === (int) $course->department_id
            && (int) $this->year_of_study === (int) $course->semester;
    }
}
