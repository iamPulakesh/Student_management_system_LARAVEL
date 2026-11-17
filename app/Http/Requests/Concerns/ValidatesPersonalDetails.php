<?php

/**
 * Shared validation rules for name, phone, gender and date of birth.
 */

namespace App\Http\Requests\Concerns;

use App\Models\Student;
use Illuminate\Validation\Rule;

trait ValidatesPersonalDetails
{
    /**
     * A person's name: letters, spaces, dots, apostrophes and hyphens
     * (e.g. "Dr. Ananya Sen", "D'Souza", "Ray-Chaudhuri").
     *
     * @return list<string>
     */
    protected function personNameRules(int $maxLength): array
    {
        return ['required', 'string', "max:{$maxLength}", "regex:/^\\pL[\\pL\\s.'-]*$/u"];
    }

    /**
     * An optional Indian mobile number: 10 digits starting with 6-9,
     * optionally prefixed with +91.
     *
     * @return list<string>
     */
    protected function phoneRules(): array
    {
        return ['nullable', 'string', 'regex:/^(\+91[\s-]?)?[6-9]\d{9}$/'];
    }

    /**
     * An optional gender from the options shown on the forms.
     *
     * @return list<mixed>
     */
    protected function genderRules(): array
    {
        return ['nullable', 'string', Rule::in(Student::GENDERS)];
    }

    /**
     * An optional date of birth for a realistic student age.
     *
     * @return list<string>
     */
    protected function dateOfBirthRules(): array
    {
        return [
            'nullable',
            'date',
            'before_or_equal:'.now()->subYears(Student::MIN_AGE)->toDateString(),
            'after_or_equal:'.now()->subYears(Student::MAX_AGE)->toDateString(),
        ];
    }

    /**
     * Friendly messages for the rules above.
     *
     * @return array<string, string>
     */
    protected function personalDetailMessages(): array
    {
        $nameFormat = 'The :attribute may only contain letters, spaces, dots, apostrophes and hyphens.';

        return [
            'name.regex' => $nameFormat,
            'first_name.regex' => $nameFormat,
            'last_name.regex' => $nameFormat,
            'phone.regex' => 'Enter a valid 10-digit mobile number, e.g. 9876543210.',
            'gender.in' => 'Please choose '.implode(', ', Student::GENDERS).'.',
            'date_of_birth.before_or_equal' => 'A student must be at least '.Student::MIN_AGE.' years old.',
            'date_of_birth.after_or_equal' => 'Please enter a realistic date of birth (age '.Student::MAX_AGE.' or under).',
        ];
    }
}
