<?php

/**
 * Seeds the demo courses.
 */

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Courses for Semesters 1, 3 and 5, grouped by department code.
     *
     * A null teacher leaves the course unassigned. 4-credit courses meet
     * Mon/Wed/Fri and 3-credit courses meet Tue/Thu (see AttendanceSeeder).
     *
     * @var array<string, list<array{code: string, name: string, credits: int, semester: int, teacher: string|null}>>
     */
    private const COURSES_BY_DEPARTMENT = [
        'CSE' => [
            ['code' => 'CS101', 'name' => 'Programming Fundamentals', 'credits' => 4, 'semester' => 1, 'teacher' => 'F103'],
            ['code' => 'CS102', 'name' => 'Discrete Mathematics', 'credits' => 3, 'semester' => 1, 'teacher' => 'F102'],
            ['code' => 'CS301', 'name' => 'Data Structures', 'credits' => 4, 'semester' => 3, 'teacher' => 'F101'],
            ['code' => 'CS302', 'name' => 'Database Management Systems', 'credits' => 4, 'semester' => 3, 'teacher' => 'F102'],
            ['code' => 'CS303', 'name' => 'Object Oriented Programming', 'credits' => 3, 'semester' => 3, 'teacher' => 'F103'],
            ['code' => 'CS501', 'name' => 'Operating Systems', 'credits' => 4, 'semester' => 5, 'teacher' => 'F101'],
            ['code' => 'CS502', 'name' => 'Computer Networks', 'credits' => 3, 'semester' => 5, 'teacher' => 'F103'],
            ['code' => 'CS503', 'name' => 'Machine Learning', 'credits' => 3, 'semester' => 5, 'teacher' => null],
        ],
        'ECE' => [
            ['code' => 'EC101', 'name' => 'Basic Electronics', 'credits' => 4, 'semester' => 1, 'teacher' => 'F201'],
            ['code' => 'EC301', 'name' => 'Analog Electronics', 'credits' => 4, 'semester' => 3, 'teacher' => 'F201'],
            ['code' => 'EC302', 'name' => 'Digital Logic Design', 'credits' => 4, 'semester' => 3, 'teacher' => 'F202'],
            ['code' => 'EC303', 'name' => 'Signals and Systems', 'credits' => 3, 'semester' => 3, 'teacher' => 'F203'],
            ['code' => 'EC501', 'name' => 'Communication Systems', 'credits' => 4, 'semester' => 5, 'teacher' => 'F202'],
        ],
        'ME' => [
            ['code' => 'ME101', 'name' => 'Engineering Mechanics', 'credits' => 4, 'semester' => 1, 'teacher' => 'F301'],
            ['code' => 'ME301', 'name' => 'Thermodynamics', 'credits' => 4, 'semester' => 3, 'teacher' => 'F301'],
            ['code' => 'ME302', 'name' => 'Fluid Mechanics', 'credits' => 4, 'semester' => 3, 'teacher' => 'F302'],
            ['code' => 'ME303', 'name' => 'Strength of Materials', 'credits' => 3, 'semester' => 3, 'teacher' => 'F303'],
            ['code' => 'ME501', 'name' => 'Heat Transfer', 'credits' => 3, 'semester' => 5, 'teacher' => 'F302'],
        ],
    ];

    /**
     * Seed the courses and assign their faculty members.
     */
    public function run(): void
    {
        foreach (self::COURSES_BY_DEPARTMENT as $departmentCode => $courses) {
            $departmentId = Department::query()->where('code', $departmentCode)->value('id');

            foreach ($courses as $course) {
                Course::query()->firstOrCreate(
                    ['code' => $course['code']],
                    [
                        'name' => $course['name'],
                        'credits' => $course['credits'],
                        'semester' => $course['semester'],
                        'department_id' => $departmentId,
                        'teacher_id' => $course['teacher'] === null
                            ? null
                            : Teacher::query()->where('employee_number', $course['teacher'])->value('id'),
                    ],
                );
            }
        }
    }
}
