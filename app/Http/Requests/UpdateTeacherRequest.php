<?php

/**
 * Validates faculty edits by the admin.
 */

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesPersonalDetails;
use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateTeacherRequest extends FormRequest
{
    use ValidatesPersonalDetails;

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('employee_number')) {
            $this->merge([
                'employee_number' => strtoupper(trim((string) $this->input('employee_number'))),
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
        /** @var Teacher $teacher */
        $teacher = $this->route('teacher');

        return [
            'employee_number' => [
                'required',
                'string',
                'max:4',
                'regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('teachers', 'employee_number')->ignore($teacher->id),
            ],
            'name' => $this->personNameRules(255),
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'phone' => $this->phoneRules(),
            'password' => ['nullable', 'string', Password::min(8), 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    /**
     * Faculty only teach courses of their own department, so block moving a
     * faculty member who still has courses to another department.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('department_id')) {
                    return;
                }

                /** @var Teacher $teacher */
                $teacher = $this->route('teacher');

                if ($this->integer('department_id') !== (int) $teacher->department_id && $teacher->courses()->exists()) {
                    $validator->errors()->add(
                        'department_id',
                        'Cannot move a faculty member who teaches courses to another department. Reassign their courses first.',
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
