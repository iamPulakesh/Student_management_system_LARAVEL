<?php

/**
 * Validates the new-course form.
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }

        if ($this->has('name')) {
            $this->merge([
                'name' => trim((string) $this->input('name')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:30', 'alpha_dash:ascii', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'teacher_id' => [
                'nullable',
                'integer',
                Rule::exists('teachers', 'id')->where('department_id', $this->integer('department_id')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'teacher_id.exists' => 'The assigned faculty member must belong to the course\'s department.',
        ];
    }
}
