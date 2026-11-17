<?php

/**
 * Seeds the demo students and their login accounts.
 */

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\Concerns\UsesStableRandomness;
use Illuminate\Database\Seeder;
use Random\Randomizer;

class StudentSeeder extends Seeder
{
    use UsesStableRandomness;

    /**
     * How many students to create per department and semester.
     * CSE has more than 10 so the paginated student list shows a second page.
     *
     * @var array<string, array<int, int>>
     */
    private const STUDENT_COUNTS = [
        'CSE' => [1 => 4, 3 => 6, 5 => 3],
        'ECE' => [1 => 3, 3 => 4, 5 => 2],
        'ME' => [1 => 2, 3 => 4, 5 => 2],
    ];

    /**
     * Admission-year prefix used in registration numbers, by semester.
     *
     * @var array<int, string>
     */
    private const BATCH_BY_SEMESTER = [1 => '24', 3 => '23', 5 => '22'];

    /**
     * Fixed students used as the well-known demo logins.
     *
     * @var array<string, array{first_name: string, last_name: string, gender: string, date_of_birth: string, phone: string, address: string}>
     */
    private const FIXED_STUDENTS = [
        'CSE23001' => ['first_name' => 'Aarav', 'last_name' => 'Sharma', 'gender' => 'Male', 'date_of_birth' => '2005-03-14', 'phone' => '9000000001', 'address' => '12 Lake Road, Kolkata'],
        'CSE23002' => ['first_name' => 'Priya', 'last_name' => 'Das', 'gender' => 'Female', 'date_of_birth' => '2005-07-22', 'phone' => '9000000002', 'address' => '45 Park Street, Kolkata'],
        'CSE23003' => ['first_name' => 'Rohan', 'last_name' => 'Banerjee', 'gender' => 'Male', 'date_of_birth' => '2004-11-05', 'phone' => '9000000003', 'address' => '7 Salt Lake Sector V, Kolkata'],
    ];

    /** @var list<string> */
    private const MALE_FIRST_NAMES = [
        'Arjun', 'Kabir', 'Vikram', 'Ishaan', 'Aditya', 'Rahul', 'Sourav', 'Debashis', 'Nikhil', 'Arnab',
        'Siddharth', 'Rehan', 'Tanmay', 'Ankit', 'Kunal', 'Pritam', 'Yash', 'Farhan', 'Joydeep', 'Manish',
    ];

    /** @var list<string> */
    private const FEMALE_FIRST_NAMES = [
        'Ishita', 'Ritika', 'Tanvi', 'Ananya', 'Sneha', 'Meghna', 'Riya', 'Pooja', 'Shreya', 'Aisha',
        'Kavya', 'Moumita', 'Diya', 'Sayani', 'Nandini', 'Trisha', 'Zoya', 'Payel', 'Neha', 'Sanjana',
    ];

    /** @var list<string> */
    private const LAST_NAMES = [
        'Chatterjee', 'Mehta', 'Kapoor', 'Singh', 'Bose', 'Rao', 'Mukherjee', 'Ghosh', 'Dutta', 'Sarkar',
        'Gupta', 'Iyer', 'Khan', 'Saha', 'Nair', 'Reddy', 'Paul', 'Mondal', 'Basu', 'Verma', 'Joshi', 'Roy',
    ];

    /** @var list<string> */
    private const LOCALITIES = [
        'Gariahat Road, Kolkata', 'New Town Action Area I, Kolkata', 'Behala Chowrasta, Kolkata', 'Dum Dum Road, Kolkata',
        'Ballygunge Place, Kolkata', 'Howrah Maidan, Howrah', 'Jadavpur, Kolkata', 'Barasat, North 24 Parganas',
        'Rajarhat Main Road, Kolkata', 'Tollygunge, Kolkata', 'Shyambazar, Kolkata', 'Bidhannagar, Kolkata',
        'Serampore, Hooghly', 'Durgapur City Centre, Durgapur', 'Siliguri Hill Cart Road, Siliguri',
    ];

    /**
     * Percentage chance that an optional profile field is left empty,
     * so some profiles show "Not provided".
     */
    private const MISSING_FIELD_CHANCE = 15;

    /** @var array<string, true> */
    private array $usedNames = [];

    /**
     * Seed students across Semesters 1, 3 and 5, each with a linked login account.
     */
    public function run(): void
    {
        $randomizer = $this->randomizerFor('students');

        foreach (self::FIXED_STUDENTS as $student) {
            $this->usedNames["{$student['first_name']} {$student['last_name']}"] = true;
        }

        foreach (self::STUDENT_COUNTS as $departmentCode => $countsBySemester) {
            $departmentId = Department::query()->where('code', $departmentCode)->value('id');

            foreach ($countsBySemester as $semester => $count) {
                for ($sequence = 1; $sequence <= $count; $sequence++) {
                    $studentNumber = $departmentCode.self::BATCH_BY_SEMESTER[$semester].sprintf('%03d', $sequence);
                    $details = self::FIXED_STUDENTS[$studentNumber] ?? $this->randomDetails($randomizer, $semester);

                    $this->createStudent($studentNumber, $details, $departmentId, $semester);
                }
            }
        }
    }

    /**
     * Generate a random but realistic student profile.
     *
     * @return array{first_name: string, last_name: string, gender: string, date_of_birth: string|null, phone: string|null, address: string|null}
     */
    private function randomDetails(Randomizer $randomizer, int $semester): array
    {
        do {
            $isFemale = $randomizer->getInt(0, 1) === 1;
            $firstNames = $isFemale ? self::FEMALE_FIRST_NAMES : self::MALE_FIRST_NAMES;
            $firstName = $firstNames[$randomizer->getInt(0, count($firstNames) - 1)];
            $lastName = self::LAST_NAMES[$randomizer->getInt(0, count(self::LAST_NAMES) - 1)];
        } while (
            isset($this->usedNames["{$firstName} {$lastName}"])
            || User::query()->where('email', $this->emailFor($firstName, $lastName))->exists()
        );

        $this->usedNames["{$firstName} {$lastName}"] = true;

        $birthYear = 2008 - intdiv($semester - 1, 2) - $randomizer->getInt(0, 1);
        $dateOfBirth = sprintf('%d-%02d-%02d', $birthYear, $randomizer->getInt(1, 12), $randomizer->getInt(1, 28));
        $phone = [9, 8, 7, 6][$randomizer->getInt(0, 3)].sprintf('%09d', $randomizer->getInt(0, 999_999_999));
        $address = $randomizer->getInt(1, 220).' '.self::LOCALITIES[$randomizer->getInt(0, count(self::LOCALITIES) - 1)];

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => $isFemale ? 'Female' : 'Male',
            'date_of_birth' => $this->maybeMissing($randomizer, $dateOfBirth),
            'phone' => $this->maybeMissing($randomizer, $phone),
            'address' => $this->maybeMissing($randomizer, $address),
        ];
    }

    /**
     * Build the demo login email for a student name.
     */
    private function emailFor(string $firstName, string $lastName): string
    {
        return strtolower("{$firstName}.{$lastName}").'@sms.test';
    }

    /**
     * Return the value, or null for a small share of profiles.
     */
    private function maybeMissing(Randomizer $randomizer, string $value): ?string
    {
        return $randomizer->getInt(1, 100) <= self::MISSING_FIELD_CHANCE ? null : $value;
    }

    /**
     * Create the login account and student profile, unless the student already exists.
     *
     * @param  array{first_name: string, last_name: string, gender: string, date_of_birth: string|null, phone: string|null, address: string|null}  $details
     */
    private function createStudent(string $studentNumber, array $details, int $departmentId, int $semester): void
    {
        if (Student::query()->where('student_number', $studentNumber)->exists()) {
            return;
        }

        $email = $this->emailFor($details['first_name'], $details['last_name']);

        $user = User::query()->where('email', $email)->first()
            ?? User::createWithRole([
                'name' => "{$details['first_name']} {$details['last_name']}",
                'email' => $email,
                'password' => 'password',
            ], User::ROLE_STUDENT);

        Student::query()->create([
            'student_number' => $studentNumber,
            'first_name' => $details['first_name'],
            'last_name' => $details['last_name'],
            'phone' => $details['phone'],
            'date_of_birth' => $details['date_of_birth'],
            'gender' => $details['gender'],
            'address' => $details['address'],
            'department_id' => $departmentId,
            'user_id' => $user->id,
            'year_of_study' => $semester,
        ]);
    }
}
