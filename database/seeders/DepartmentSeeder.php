<?php

/**
 * Seeds the demo departments.
 */

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed the academic departments.
     *
     * Civil Engineering is intentionally left empty (no faculty, students or
     * courses) so deleting a department can be demonstrated successfully.
     */
    public function run(): void
    {
        $departments = [
            ['code' => 'CSE', 'name' => 'Computer Science and Engineering'],
            ['code' => 'ECE', 'name' => 'Electronics and Communication Engineering'],
            ['code' => 'ME', 'name' => 'Mechanical Engineering'],
            ['code' => 'CE', 'name' => 'Civil Engineering'],
        ];

        foreach ($departments as $department) {
            Department::query()->firstOrCreate(
                ['code' => $department['code']],
                ['name' => $department['name']],
            );
        }
    }
}
