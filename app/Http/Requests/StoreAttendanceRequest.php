<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return false;
        }

        if ($user->isTeacher()) {
            $teacherId = $user->teacher?->id;
            if ($teacherId === null) {
                return false;
            }

            $courseId = $this->input('course_id');
            if ($courseId !== null && is_numeric($courseId)) {
                $course = Course::find((int) $courseId);
                if ($course !== null && (int) $course->teacher_id !== (int) $teacherId) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'attendance_date' => ['required', 'date'],
            'statuses' => ['required', 'array', 'min:1'],
            'statuses.*' => [
                'required',
                'string',
                Rule::in([Attendance::STATUS_PRESENT, Attendance::STATUS_ABSENT]),
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'Please select a course.',
            'course_id.exists' => 'The selected course does not exist.',
            'attendance_date.required' => 'Please provide an attendance date.',
            'statuses.required' => 'Please mark attendance for at least one student.',
            'statuses.*.in' => 'Attendance status must be either present or absent.',
        ];
    }
}
