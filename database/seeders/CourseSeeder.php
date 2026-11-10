<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed CSE Semester 3 courses, one per faculty member.
     */
    public function run(): void
    {
        $departmentId = Department::query()->where('code', 'CSE')->value('id');

        $courses = [
            ['code' => 'CS301', 'name' => 'Data Structures', 'credits' => 4, 'teacher' => 'F101'],
            ['code' => 'CS302', 'name' => 'Database Management Systems', 'credits' => 4, 'teacher' => 'F102'],
            ['code' => 'CS303', 'name' => 'Object Oriented Programming', 'credits' => 3, 'teacher' => 'F103'],
        ];

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
