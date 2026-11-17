<?php

/**
 * Validates a new assessment mark.
 */

namespace App\Http\Requests;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAssessmentRequest extends FormRequest
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
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'assessment_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('assessments', 'assessment_name')->where(function ($query) {
                    return $query
                        ->where('student_id', $this->input('student_id'))
                        ->where('course_id', $this->input('course_id'));
                }),
            ],
            'marks_obtained' => ['required', 'integer', 'min:0', 'max:999', 'lte:max_marks'],
            'max_marks' => ['required', 'integer', 'gt:0', 'max:999'],
            'assessment_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.Assessment::earliestRecordableDate()],
        ];
    }

    /**
     * Configure the validator instance to check the student is enrolled in the
     * course and that the assessment keeps one maximum mark for the whole class.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['course_id', 'assessment_name', 'max_marks'])) {
                    return;
                }

                $maxMarksInUse = Assessment::maxMarksInUse($this->integer('course_id'), $this->input('assessment_name'));

                if ($maxMarksInUse !== null && $maxMarksInUse !== $this->integer('max_marks')) {
                    $validator->errors()->add(
                        'max_marks',
                        "{$this->input('assessment_name')} in this course is marked out of {$maxMarksInUse}. Use the same maximum marks.",
                    );
                }
            },
            function (Validator $validator) {
                $courseId = $this->input('course_id');
                $studentId = $this->input('student_id');

                if (! is_numeric($courseId) || ! is_numeric($studentId)) {
                    return;
                }

                $course = Course::find((int) $courseId);
                $student = Student::find((int) $studentId);

                if ($course !== null && $student !== null && ! $student->isEnrolledIn($course)) {
                    $validator->errors()->add(
                        'student_id',
                        'The selected student must be in the same department and semester as the selected course.'
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
            'assessment_name.unique' => 'An assessment with this name already exists for this student in the selected course.',
            'marks_obtained.lte' => 'Marks obtained cannot exceed maximum marks.',
            'marks_obtained.min' => 'Marks obtained cannot be negative.',
            'max_marks.gt' => 'Maximum marks must be greater than zero.',
            'assessment_date.required' => 'Please enter the assessment date.',
            'assessment_date.after_or_equal' => 'The assessment date cannot be more than '.Assessment::RECORDING_WINDOW_MONTHS.' months ago.',
        ];
    }
}
