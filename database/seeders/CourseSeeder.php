<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Semester 3 courses grouped by department code, each assigned to one faculty member.
     *
     * @var array<string, list<array{code: string, name: string, credits: int, teacher: string}>>
     */
    private const COURSES_BY_DEPARTMENT = [
        'CSE' => [
            ['code' => 'CS301', 'name' => 'Data Structures', 'credits' => 4, 'teacher' => 'F101'],
            ['code' => 'CS302', 'name' => 'Database Management Systems', 'credits' => 4, 'teacher' => 'F102'],
            ['code' => 'CS303', 'name' => 'Object Oriented Programming', 'credits' => 3, 'teacher' => 'F103'],
        ],
        'ECE' => [
            ['code' => 'EC301', 'name' => 'Analog Electronics', 'credits' => 4, 'teacher' => 'F201'],
            ['code' => 'EC302', 'name' => 'Digital Logic Design', 'credits' => 4, 'teacher' => 'F202'],
            ['code' => 'EC303', 'name' => 'Signals and Systems', 'credits' => 3, 'teacher' => 'F203'],
        ],
        'ME' => [
            ['code' => 'ME301', 'name' => 'Thermodynamics', 'credits' => 4, 'teacher' => 'F301'],
            ['code' => 'ME302', 'name' => 'Fluid Mechanics', 'credits' => 4, 'teacher' => 'F302'],
            ['code' => 'ME303', 'name' => 'Strength of Materials', 'credits' => 3, 'teacher' => 'F303'],
        ],
    ];

    /**
     * Seed three Semester 3 courses per department.
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
                        'semester' => 3,
                        'department_id' => $departmentId,
                        'teacher_id' => Teacher::query()->where('employee_number', $course['teacher'])->value('id'),
                    ],
                );
            }
        }
    }
}
