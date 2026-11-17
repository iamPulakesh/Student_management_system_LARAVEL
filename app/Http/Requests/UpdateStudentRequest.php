<?php

/**
 * Validates student edits by the admin.
 */

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesPersonalDetails;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateStudentRequest extends FormRequest
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
        /** @var Student $student */
        $student = $this->route('student');

        return [
            'student_number' => [
                'required',
                'string',
                'max:8',
                'regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('students', 'student_number')->ignore($student->id),
            ],
            'first_name' => $this->personNameRules(100),
            'last_name' => $this->personNameRules(100),
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'password' => ['nullable', 'string', Password::min(8), 'max:255'],
            'date_of_birth' => $this->dateOfBirthRules(),
            'gender' => $this->genderRules(),
            'phone' => $this->phoneRules(),
            'address' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'year_of_study' => ['required', 'integer', 'min:1', 'max:8'],
        ];
    }

    /**
     * Block moving a student to another department or semester once attendance
     * or marks exist, since those records belong to their current courses.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['department_id', 'year_of_study'])) {
                    return;
                }

                /** @var Student $student */
                $student = $this->route('student');

                $changedFields = array_filter([
                    'department_id' => $this->integer('department_id') !== (int) $student->department_id ? 'department' : null,
                    'year_of_study' => $this->integer('year_of_study') !== (int) $student->year_of_study ? 'semester' : null,
                ]);

                if ($changedFields === [] || ! $student->hasAcademicRecords()) {
                    return;
                }

                foreach ($changedFields as $field => $label) {
                    $validator->errors()->add(
                        $field,
                        "Cannot change the {$label} of a student who already has attendance or assessment records.",
                    );
                }
            },
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
