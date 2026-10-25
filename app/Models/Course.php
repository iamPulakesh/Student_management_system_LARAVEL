<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'credits',
        'semester',
        'department_id',
        'teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'semester' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
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
     * Limit the query to courses the student takes: same department and the
     * course's semester matches the student's semester.
     */
    public function scopeForStudent(Builder $query, Student $student): Builder
    {
        return $query->where('department_id', $student->department_id)
            ->where('semester', $student->year_of_study);
    }
}
