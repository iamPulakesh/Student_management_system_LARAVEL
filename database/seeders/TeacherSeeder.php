<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Faculty members grouped by department code.
     *
     * @var array<string, list<array{employee_number: string, name: string, email: string, phone: string}>>
     */
    private const TEACHERS_BY_DEPARTMENT = [
        'CSE' => [
            ['employee_number' => 'F101', 'name' => 'Dr. Ananya Sen', 'email' => 'ananya.sen@sms.test', 'phone' => '9830000101'],
            ['employee_number' => 'F102', 'name' => 'Prof. Rajib Ghosh', 'email' => 'rajib.ghosh@sms.test', 'phone' => '9830000102'],
            ['employee_number' => 'F103', 'name' => 'Dr. Meera Iyer', 'email' => 'meera.iyer@sms.test', 'phone' => '9830000103'],
        ],
        'ECE' => [
            ['employee_number' => 'F201', 'name' => 'Dr. Subhajit Roy', 'email' => 'subhajit.roy@sms.test', 'phone' => '9830000201'],
            ['employee_number' => 'F202', 'name' => 'Prof. Kavita Nair', 'email' => 'kavita.nair@sms.test', 'phone' => '9830000202'],
            ['employee_number' => 'F203', 'name' => 'Dr. Arindam Paul', 'email' => 'arindam.paul@sms.test', 'phone' => '9830000203'],
        ],
        'ME' => [
            ['employee_number' => 'F301', 'name' => 'Dr. Sanjay Mukherjee', 'email' => 'sanjay.mukherjee@sms.test', 'phone' => '9830000301'],
            ['employee_number' => 'F302', 'name' => 'Prof. Neha Verma', 'email' => 'neha.verma@sms.test', 'phone' => '9830000302'],
            ['employee_number' => 'F303', 'name' => 'Dr. Amit Kulkarni', 'email' => 'amit.kulkarni@sms.test', 'phone' => '9830000303'],
        ],
    ];

    /**
     * Seed three faculty members per department, each with a linked login account.
     */
    public function run(): void
    {
        foreach (self::TEACHERS_BY_DEPARTMENT as $departmentCode => $teachers) {
            $departmentId = Department::query()->where('code', $departmentCode)->value('id');

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
}
