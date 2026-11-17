<?php

/**
 * Validates the new-faculty form.
 */

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesPersonalDetails;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreTeacherRequest extends FormRequest
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
        return [
            'employee_number' => ['required', 'string', 'max:4', 'regex:/^[a-zA-Z0-9]+$/', 'unique:teachers,employee_number'],
            'name' => $this->personNameRules(255),
            'email' => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'phone' => $this->phoneRules(),
            'password' => ['required', 'string', Password::min(8), 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
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
