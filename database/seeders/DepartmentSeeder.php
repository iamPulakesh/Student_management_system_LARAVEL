<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed the academic departments.
     */
    public function run(): void
    {
        $departments = [
            ['code' => 'CSE', 'name' => 'Computer Science and Engineering'],
            ['code' => 'ECE', 'name' => 'Electronics and Communication Engineering'],
            ['code' => 'ME', 'name' => 'Mechanical Engineering'],
        ];

        foreach ($departments as $department) {
            Department::query()->firstOrCreate(
                ['code' => $department['code']],
                ['name' => $department['name']],
            );
        }
    }
}
