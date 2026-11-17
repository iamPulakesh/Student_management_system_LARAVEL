<?php

/**
 * Validates the new-student form.
 */

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesPersonalDetails;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStudentRequest extends FormRequest
{
    use ValidatesPersonalDetails;

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('student_number')) {
            $this->merge([
                'student_number' => strtoupper(trim((string) $this->input('student_number'))),
            ]);
        }

        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim((string) $this->input('email'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_number' => ['required', 'string', 'max:8', 'regex:/^[a-zA-Z0-9]+$/', 'unique:students,student_number'],
            'first_name' => $this->personNameRules(100),
            'last_name' => $this->personNameRules(100),
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8), 'max:255'],
            'date_of_birth' => $this->dateOfBirthRules(),
            'gender' => $this->genderRules(),
            'phone' => $this->phoneRules(),
            'address' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'year_of_study' => ['required', 'integer', 'min:1', 'max:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->personalDetailMessages();
    }
}
