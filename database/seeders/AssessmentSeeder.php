<?php

/**
 * Seeds demo assessment marks.
 */

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\UsesStableRandomness;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    use UsesStableRandomness;

    /**
     * Assessments held so far this term. The Mid-Term only exists from
     * Semester 3 onwards. "Quiz 2" is deliberately left free so a mark can
     * be recorded live during a demo.
     *
     * @var list<array{name: string, max_marks: int, weekdays_ago: int, min_semester: int}>
     */
    private const ASSESSMENTS = [
        ['name' => 'Quiz 1', 'max_marks' => 20, 'weekdays_ago' => 12, 'min_semester' => 1],
        ['name' => 'Assignment 1', 'max_marks' => 10, 'weekdays_ago' => 7, 'min_semester' => 1],
        ['name' => 'Mid-Term', 'max_marks' => 50, 'weekdays_ago' => 3, 'min_semester' => 3],
    ];

    /**
     * Range of each student's typical score (as a share of maximum marks),
     * and how far a single result can swing from it.
     */
    private const MIN_ABILITY = 0.35;

    private const MAX_ABILITY = 0.95;

    private const MAX_SWING = 0.15;

    /**
     * Seed marks for every enrolled student in every assigned course.
     * Each student has a consistent ability level, so strong students score
     * well across courses while individual results still vary.
     */
    public function run(): void
    {
        $courses = Course::query()->with('teacher')->whereNotNull('teacher_id')->get();

        foreach ($courses as $course) {
            $students = Student::query()->enrolledIn($course)->orderBy('student_number')->get();

            foreach (self::ASSESSMENTS as $assessment) {
                if ($course->semester < $assessment['min_semester']) {
                    continue;
                }

                foreach ($students as $student) {
                    Assessment::query()->firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                            'assessment_name' => $assessment['name'],
                        ],
                        [
                            'marks_obtained' => $this->marksFor($student, $course, $assessment['name'], $assessment['max_marks']),
                            'max_marks' => $assessment['max_marks'],
                            'assessment_date' => CarbonImmutable::today()->subWeekdays($assessment['weekdays_ago'])->toDateString(),
                            'recorded_by_user_id' => $course->teacher->user_id,
                        ],
                    );
                }
            }
        }
    }

    /**
     * Work out a believable mark from the student's ability plus some variation.
     */
    private function marksFor(Student $student, Course $course, string $assessmentName, int $maxMarks): int
    {
        $ability = $this->randomizerFor("ability:{$student->student_number}")
            ->getFloat(self::MIN_ABILITY, self::MAX_ABILITY);
        $swing = $this->randomizerFor("marks:{$student->student_number}:{$course->code}:{$assessmentName}")
            ->getFloat(-self::MAX_SWING, self::MAX_SWING);

        return (int) max(0, min($maxMarks, round(($ability + $swing) * $maxMarks)));
    }
}
