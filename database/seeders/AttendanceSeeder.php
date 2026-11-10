<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Absent days per student, keyed by student number. Each number is an
     * index into the class dates (0 = oldest). This gives roughly 100%,
     * 67% and 33% attendance so the 75% badge shows on some rows only.
     *
     * @var array<string, list<int>>
     */
    private const ABSENT_DAYS = [
        'CSE23001' => [],
        'CSE23002' => [1],
        'CSE23003' => [0, 2],
    ];

    /**
     * Seed attendance for the last three weekdays, leaving today unrecorded
     * so attendance can be taken live during a demo.
     */
    public function run(): void
    {
        $classDates = $this->previousWeekdays(3);

        foreach (Course::query()->with('teacher')->get() as $course) {
            $students = Student::query()->enrolledIn($course)->get();

            foreach ($classDates as $dayIndex => $classDate) {
                foreach ($students as $student) {
                    $isAbsent = in_array($dayIndex, self::ABSENT_DAYS[$student->student_number] ?? [], true);

                    Attendance::query()->firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                            'attendance_date' => $classDate,
                        ],
                        [
                            'status' => $isAbsent ? Attendance::STATUS_ABSENT : Attendance::STATUS_PRESENT,
                            'recorded_by_user_id' => $course->teacher->user_id,
                        ],
                    );
                }
            }
        }
    }

    /**
     * Get the given number of weekdays before today, oldest first.
     *
     * @return list<string>
     */
    private function previousWeekdays(int $count): array
    {
        $dates = [];
        $date = CarbonImmutable::today();

        while (count($dates) < $count) {
            $date = $date->subDay();

            if ($date->isWeekday()) {
                $dates[] = $date->toDateString();
            }
        }

        return array_reverse($dates);
    }
}
