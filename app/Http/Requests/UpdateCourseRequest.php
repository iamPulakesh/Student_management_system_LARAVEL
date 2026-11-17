<?php

/**
 * Validates course edits.
 */

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCourseRequest extends FormRequest
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
        /** @var Course $course */
        $course = $this->route('course');

        return [
            'code' => [
                'required',
                'string',
                'max:30',
                'alpha_dash:ascii',
                Rule::unique('courses', 'code')->ignore($course->id),
            ],
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
     * Block moving a course to another department or semester once attendance
     * or marks exist, since that would change which students are enrolled.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['department_id', 'semester'])) {
                    return;
                }

                /** @var Course $course */
                $course = $this->route('course');

                $changedFields = array_filter([
                    'department_id' => $this->integer('department_id') !== (int) $course->department_id ? 'department' : null,
                    'semester' => $this->integer('semester') !== (int) $course->semester ? 'semester' : null,
                ]);

                if ($changedFields === [] || ! $course->hasAcademicRecords()) {
                    return;
                }

                foreach ($changedFields as $field => $label) {
                    $validator->errors()->add(
                        $field,
                        "Cannot change the {$label} of a course that already has attendance or assessment records.",
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
        return [
            'teacher_id.exists' => 'The assigned faculty member must belong to the course\'s department.',
        ];
    }
}
