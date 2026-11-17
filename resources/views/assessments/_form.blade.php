{{--
    Shared assessment-mark form fields.
--}}

@php
    $selectedCourseId = old('course_id', $assessment->course_id ?? ($selectedCourse->id ?? ''));
    $selectedStudentId = old('student_id', $assessment->student_id ?? '');
    $assessmentDateValue = old(
        'assessment_date',
        isset($assessment) && $assessment->assessment_date
            ? $assessment->assessment_date->format('Y-m-d')
            : now()->toDateString()
    );
    $existingAssessmentDate = isset($assessment) ? $assessment->assessment_date?->format('Y-m-d') : null;
    $earliestAssessmentDate = $existingAssessmentDate !== null
        ? min(\App\Models\Assessment::earliestRecordableDate(), $existingAssessmentDate)
        : \App\Models\Assessment::earliestRecordableDate();
@endphp

<div class="form-grid">
    <div class="form-group">
        <label for="course_id">Course <span class="required">*</span></label>
        <select id="course_id" name="course_id" required>
            <option value="">-- Select Course --</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" {{ (string) $selectedCourseId === (string) $course->id ? 'selected' : '' }}>
                    {{ $course->code }} — {{ $course->name }} ({{ $course->department?->code }})
                </option>
            @endforeach
        </select>
        <x-form-error field="course_id" />
    </div>

    <div class="form-group">
        <label for="student_id">Student <span class="required">*</span></label>
        <select id="student_id" name="student_id" required>
            <option value="">-- Select Student --</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" {{ (string) $selectedStudentId === (string) $student->id ? 'selected' : '' }}>
                    {{ $student->student_number }} — {{ $student->full_name }} ({{ $student->department?->code }})
                </option>
            @endforeach
        </select>
        <x-form-error field="student_id" />
    </div>

    <div class="form-group">
        <label for="assessment_name">Assessment Name <span class="required">*</span></label>
        <input
            type="text"
            id="assessment_name"
            name="assessment_name"
            value="{{ old('assessment_name', $assessment->assessment_name ?? '') }}"
            required
        >
        <x-form-error field="assessment_name" />
    </div>

    <div class="form-group">
        <label for="assessment_date">Assessment Date <span class="required">*</span></label>
        <input
            type="date"
            id="assessment_date"
            name="assessment_date"
            value="{{ $assessmentDateValue }}"
            min="{{ $earliestAssessmentDate }}"
            max="{{ now()->toDateString() }}"
            required
        >
        <x-form-error field="assessment_date" />
    </div>

    <div class="form-group">
        <label for="marks_obtained">Marks Obtained <span class="required">*</span></label>
        <input
            type="number"
            step="1"
            min="0"
            max="999"
            id="marks_obtained"
            name="marks_obtained"
            value="{{ old('marks_obtained', $assessment->marks_obtained ?? '') }}"
            required
        >
        <x-form-error field="marks_obtained" />
    </div>

    <div class="form-group">
        <label for="max_marks">Maximum Marks <span class="required">*</span></label>
        <input
            type="number"
            step="1"
            min="1"
            max="999"
            id="max_marks"
            name="max_marks"
            value="{{ old('max_marks', $assessment->max_marks ?? '100') }}"
            required
        >
        <x-form-error field="max_marks" />
    </div>
</div>

<div class="actions-group" style="margin-top: 1.25rem;">
    <button type="submit" class="btn">{{ $submitLabel ?? 'Save Assessment' }}</button>
    <a href="{{ route($routePrefix . '.assessments.index') }}" class="btn">Cancel</a>
</div>
