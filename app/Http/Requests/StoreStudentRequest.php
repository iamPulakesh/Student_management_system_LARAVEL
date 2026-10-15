<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
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
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_number' => ['required', 'string', 'max:8', 'regex:/^[a-zA-Z0-9]+$/', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'year_of_study' => ['required', 'integer', 'min:1', 'max:6'],
        ];
    }
}
