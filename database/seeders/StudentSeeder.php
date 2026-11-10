<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Semester 3 students grouped by department code.
     *
     * @var array<string, list<array{student_number: string, first_name: string, last_name: string, email: string, gender: string, date_of_birth: string, phone: string, address: string}>>
     */
    private const STUDENTS_BY_DEPARTMENT = [
        'CSE' => [
            ['student_number' => 'CSE23001', 'first_name' => 'Aarav', 'last_name' => 'Sharma', 'email' => 'aarav.sharma@sms.test', 'gender' => 'Male', 'date_of_birth' => '2005-03-14', 'phone' => '9000000001', 'address' => '12 Lake Road, Kolkata'],
            ['student_number' => 'CSE23002', 'first_name' => 'Priya', 'last_name' => 'Das', 'email' => 'priya.das@sms.test', 'gender' => 'Female', 'date_of_birth' => '2005-07-22', 'phone' => '9000000002', 'address' => '45 Park Street, Kolkata'],
            ['student_number' => 'CSE23003', 'first_name' => 'Rohan', 'last_name' => 'Banerjee', 'email' => 'rohan.banerjee@sms.test', 'gender' => 'Male', 'date_of_birth' => '2004-11-05', 'phone' => '9000000003', 'address' => '7 Salt Lake Sector V, Kolkata'],
        ],
        'ECE' => [
            ['student_number' => 'ECE23001', 'first_name' => 'Ishita', 'last_name' => 'Chatterjee', 'email' => 'ishita.chatterjee@sms.test', 'gender' => 'Female', 'date_of_birth' => '2005-01-18', 'phone' => '9000000011', 'address' => '22 Gariahat Road, Kolkata'],
            ['student_number' => 'ECE23002', 'first_name' => 'Arjun', 'last_name' => 'Mehta', 'email' => 'arjun.mehta@sms.test', 'gender' => 'Male', 'date_of_birth' => '2005-05-09', 'phone' => '9000000012', 'address' => '9 New Town Action Area I, Kolkata'],
            ['student_number' => 'ECE23003', 'first_name' => 'Ritika', 'last_name' => 'Kapoor', 'email' => 'ritika.kapoor@sms.test', 'gender' => 'Female', 'date_of_birth' => '2004-09-27', 'phone' => '9000000013', 'address' => '31 Behala Chowrasta, Kolkata'],
        ],
        'ME' => [
            ['student_number' => 'ME23001', 'first_name' => 'Kabir', 'last_name' => 'Singh', 'email' => 'kabir.singh@sms.test', 'gender' => 'Male', 'date_of_birth' => '2005-02-11', 'phone' => '9000000021', 'address' => '5 Dum Dum Road, Kolkata'],
            ['student_number' => 'ME23002', 'first_name' => 'Tanvi', 'last_name' => 'Bose', 'email' => 'tanvi.bose@sms.test', 'gender' => 'Female', 'date_of_birth' => '2005-08-30', 'phone' => '9000000022', 'address' => '18 Ballygunge Place, Kolkata'],
            ['student_number' => 'ME23003', 'first_name' => 'Vikram', 'last_name' => 'Rao', 'email' => 'vikram.rao@sms.test', 'gender' => 'Male', 'date_of_birth' => '2004-12-03', 'phone' => '9000000023', 'address' => '40 Howrah Maidan, Howrah'],
        ],
    ];

    /**
     * Seed three Semester 3 students per department, each with a linked login account.
     */
    public function run(): void
    {
        foreach (self::STUDENTS_BY_DEPARTMENT as $departmentCode => $students) {
            $departmentId = Department::query()->where('code', $departmentCode)->value('id');

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
}
