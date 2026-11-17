<?php

/**
 * Seeds three weeks of demo attendance.
 */

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Seeders\Concerns\UsesStableRandomness;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    use UsesStableRandomness;

    /**
     * How many weekdays before today to record (three weeks of classes).
     */
    private const WEEKDAYS_TO_RECORD = 15;

    /**
     * Days of the week a course meets, by its credits.
     *
     * @var array<int, list<int>>
     */
    private const MEETING_DAYS_BY_CREDITS = [
        4 => [CarbonInterface::MONDAY, CarbonInterface::WEDNESDAY, CarbonInterface::FRIDAY],
        3 => [CarbonInterface::TUESDAY, CarbonInterface::THURSDAY],
    ];

    /**
     * Lowest and highest chance that a student attends a given class.
     * Each student gets their own rate in this range, so some end up
     * above the 75% mark and some below it.
     */
    private const MIN_ATTENDANCE_RATE = 0.5;

    private const MAX_ATTENDANCE_RATE = 1.0;

    /**
     * Seed attendance for three weeks of classes, leaving today unrecorded
     * so attendance can be taken live during a demo. Unassigned courses get
     * no attendance, since no faculty member could have recorded it.
     */
    public function run(): void
    {
        $weekdays = $this->previousWeekdays(self::WEEKDAYS_TO_RECORD);

        $courses = Course::query()->with('teacher')->whereNotNull('teacher_id')->get();

        foreach ($courses as $course) {
            $meetingDays = self::MEETING_DAYS_BY_CREDITS[$course->credits] ?? self::MEETING_DAYS_BY_CREDITS[3];
            $classDates = array_filter(
                $weekdays,
                fn (CarbonImmutable $date): bool => in_array($date->dayOfWeek, $meetingDays, true),
            );

            $students = Student::query()->enrolledIn($course)->orderBy('student_number')->get();

            foreach ($students as $student) {
                $attendanceRate = $this->randomizerFor("attendance-rate:{$student->student_number}")
                    ->getFloat(self::MIN_ATTENDANCE_RATE, self::MAX_ATTENDANCE_RATE);
                $randomizer = $this->randomizerFor("attendance:{$student->student_number}:{$course->code}");

                foreach ($classDates as $classDate) {
                    Attendance::query()->firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'course_id' => $course->id,
                            'attendance_date' => $classDate->toDateString(),
                        ],
                        [
                            'status' => $randomizer->nextFloat() < $attendanceRate
                                ? Attendance::STATUS_PRESENT
                                : Attendance::STATUS_ABSENT,
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
     * @return list<CarbonImmutable>
     */
    private function previousWeekdays(int $count): array
    {
        $dates = [];
        $date = CarbonImmutable::today();

        while (count($dates) < $count) {
            $date = $date->subDay();

            if ($date->isWeekday()) {
                $dates[] = $date;
            }
        }

        return array_reverse($dates);
    }
}
