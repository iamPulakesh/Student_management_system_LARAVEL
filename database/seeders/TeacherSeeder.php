<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Seed the faculty members, each with a linked login account.
     */
    public function run(): void
    {
        $departmentId = Department::query()->where('code', 'CSE')->value('id');

        $teachers = [
            ['employee_number' => 'F101', 'name' => 'Dr. Ananya Sen', 'email' => 'ananya.sen@sms.test', 'phone' => '9830000101'],
            ['employee_number' => 'F102', 'name' => 'Prof. Rajib Ghosh', 'email' => 'rajib.ghosh@sms.test', 'phone' => '9830000102'],
            ['employee_number' => 'F103', 'name' => 'Dr. Meera Iyer', 'email' => 'meera.iyer@sms.test', 'phone' => '9830000103'],
        ];

        foreach ($teachers as $teacher) {
            if (Teacher::query()->where('employee_number', $teacher['employee_number'])->exists()) {
                continue;
            }

            $user = User::query()->where('email', $teacher['email'])->first()
                ?? User::createWithRole([
                    'name' => $teacher['name'],
                    'email' => $teacher['email'],
                    'password' => 'password',
                ], User::ROLE_TEACHER);

            Teacher::query()->create([
                'user_id' => $user->id,
                'employee_number' => $teacher['employee_number'],
                'department_id' => $departmentId,
                'phone' => $teacher['phone'],
            ]);
        }
    }
}
