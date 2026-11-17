<?php

/**
 * Validates attendance submitted for a class.
 */

namespace App\Http\Requests;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'attendance_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.Attendance::earliestRecordableDate()],
            'statuses' => ['required', 'array', 'min:1'],
            'statuses.*' => [
                'required',
                'string',
                Rule::in([Attendance::STATUS_PRESENT, Attendance::STATUS_ABSENT]),
            ],
        ];
    }

    /**
     * Require a status for every enrolled student not yet marked on this date,
     * so a class is never left half-recorded.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $course = Course::findOrFail($this->integer('course_id'));

                $alreadyMarkedIds = Attendance::query()
                    ->where('course_id', $course->id)
                    ->whereDate('attendance_date', $this->input('attendance_date'))
                    ->pluck('student_id');

                $unmarkedCount = Student::query()
                    ->enrolledIn($course)
                    ->whereNotIn('id', $alreadyMarkedIds)
                    ->whereNotIn('id', array_keys($this->input('statuses', [])))
                    ->count();

                if ($unmarkedCount > 0) {
                    $validator->errors()->add(
                        'statuses',
                        "Please mark attendance for every student in the class ({$unmarkedCount} not marked).",
                    );
                }
            },
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
            'attendance_date.after_or_equal' => 'Attendance can only be recorded for the last '.Attendance::RECORDING_WINDOW_MONTHS.' months.',
            'statuses.required' => 'Please mark attendance for at least one student.',
            'statuses.*.in' => 'Attendance status must be either present or absent.',
        ];
    }
}
