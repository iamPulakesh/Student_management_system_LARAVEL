<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * "Quiz 1" marks out of 20, keyed by student number.
     *
     * @var array<string, int>
     */
    private const QUIZ_ONE_MARKS = [
        'CSE23001' => 18,
        'CSE23002' => 14,
        'CSE23003' => 9,
    ];

    /**
     * Seed one "Quiz 1" mark per student in every course, leaving
     * "Quiz 2" free to be added live during a demo.
     */
    public function run(): void
    {
        foreach (Course::query()->with('teacher')->get() as $course) {
            foreach (Student::query()->enrolledIn($course)->get() as $student) {
                Assessment::query()->firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'assessment_name' => 'Quiz 1',
                    ],
                    [
                        'marks_obtained' => self::QUIZ_ONE_MARKS[$student->student_number] ?? 12,
                        'max_marks' => 20,
                        'assessment_date' => now()->subWeek()->toDateString(),
                        'recorded_by_user_id' => $course->teacher->user_id,
                    ],
                );
            }
        }
    }
}
