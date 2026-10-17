<div class="form-grid">
    <div class="form-group">
        <label for="code" class="form-label">Course Code (e.g. CS101) *</label>
        <input
            id="code"
            type="text"
            name="code"
            value="{{ old('code', $course->code) }}"
            class="form-control"
            required
        >
        <x-form-error field="code" />
    </div>

    <div class="form-group">
        <label for="name" class="form-label">Course Name *</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $course->name) }}"
            class="form-control"
            required
        >
        <x-form-error field="name" />
    </div>

    <div class="form-group">
        <label for="credits" class="form-label">Credits (1–10) *</label>
        <input
            id="credits"
            type="number"
            name="credits"
            min="1"
            max="10"
            value="{{ old('credits', $course->credits ?? 3) }}"
            class="form-control"
            required
        >
        <x-form-error field="credits" />
    </div>

    <div class="form-group">
        <label for="semester" class="form-label">Semester *</label>
        <select id="semester" name="semester" class="form-control" required>
            @for ($sem = 1; $sem <= 8; $sem++)
                <option
                    value="{{ $sem }}"
                    @selected((int) old('semester', $course->semester ?? 1) === $sem)
                >
                    Semester {{ $sem }}
                </option>
            @endfor
        </select>
        <x-form-error field="semester" />
    </div>

    <div class="form-group">
        <label for="department_id" class="form-label">Department *</label>
        <select id="department_id" name="department_id" class="form-control" required>
            <option value="">Select Department...</option>
            @foreach ($departments as $department)
                <option
                    value="{{ $department->id }}"
                    @selected((string) old('department_id', $course->department_id) === (string) $department->id)
                >
                    {{ $department->name }} ({{ $department->code }})
                </option>
            @endforeach
        </select>
        <x-form-error field="department_id" />
    </div>

    <div class="form-group">
        <label for="teacher_id" class="form-label">Assigned Faculty</label>
        <select id="teacher_id" name="teacher_id" class="form-control">
            <option value="">Unassigned</option>
            @foreach ($teachers as $teacher)
                <option
                    value="{{ $teacher->id }}"
                    @selected((string) old('teacher_id', $course->teacher_id) === (string) $teacher->id)
                >
                    {{ $teacher->user?->name }} ({{ $teacher->employee_number }} — {{ $teacher->department?->code }})
                </option>
            @endforeach
        </select>
        <x-form-error field="teacher_id" />
    </div>
</div>

<div class="actions-group">
    <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Course' }}</button>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
</div>
