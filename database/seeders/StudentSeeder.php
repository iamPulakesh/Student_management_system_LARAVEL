<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Seed CSE Semester 3 students, each with a linked login account.
     */
    public function run(): void
    {
        $departmentId = Department::query()->where('code', 'CSE')->value('id');

        $students = [
            [
                'student_number' => 'CSE23001',
                'first_name' => 'Aarav',
                'last_name' => 'Sharma',
                'email' => 'aarav.sharma@sms.test',
                'gender' => 'Male',
                'date_of_birth' => '2005-03-14',
                'phone' => '9000000001',
                'address' => '12 Lake Road, Kolkata',
            ],
            [
                'student_number' => 'CSE23002',
                'first_name' => 'Priya',
                'last_name' => 'Das',
                'email' => 'priya.das@sms.test',
                'gender' => 'Female',
                'date_of_birth' => '2005-07-22',
                'phone' => '9000000002',
                'address' => '45 Park Street, Kolkata',
            ],
            [
                'student_number' => 'CSE23003',
                'first_name' => 'Rohan',
                'last_name' => 'Banerjee',
                'email' => 'rohan.banerjee@sms.test',
                'gender' => 'Male',
                'date_of_birth' => '2004-11-05',
                'phone' => '9000000003',
                'address' => '7 Salt Lake Sector V, Kolkata',
            ],
        ];

        foreach ($students as $student) {
            if (Student::query()->where('student_number', $student['student_number'])->exists()) {
                continue;
            }

            $user = User::query()->where('email', $student['email'])->first()
                ?? User::createWithRole([
                    'name' => "{$student['first_name']} {$student['last_name']}",
                    'email' => $student['email'],
                    'password' => 'password',
                ], User::ROLE_STUDENT);

            Student::query()->create([
                'student_number' => $student['student_number'],
                'first_name' => $student['first_name'],
                'last_name' => $student['last_name'],
                'phone' => $student['phone'],
                'date_of_birth' => $student['date_of_birth'],
                'gender' => $student['gender'],
                'address' => $student['address'],
                'department_id' => $departmentId,
                'user_id' => $user->id,
                'year_of_study' => 3,
            ]);
        }
    }
}
