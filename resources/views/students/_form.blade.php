<div class="form-grid">
    <div class="form-group">
        <label for="student_number" class="form-label">Registration Number *</label>
        <input
            id="student_number"
            type="text"
            name="student_number"
            value="{{ old('student_number', $student->student_number) }}"
            class="form-control"
            maxlength="8"
            pattern="[A-Za-z0-9]+"
            title="Maximum 8 alphanumeric characters"
            required
        >
        <span class="form-help">Maximum 8 alphanumeric characters. No special characters.</span>
        <x-form-error field="student_number" />
    </div>

    <div class="form-group">
        <label for="first_name" class="form-label">First Name *</label>
        <input
            id="first_name"
            type="text"
            name="first_name"
            value="{{ old('first_name', $student->first_name) }}"
            class="form-control"
            required
        >
        <x-form-error field="first_name" />
    </div>

    <div class="form-group">
        <label for="last_name" class="form-label">Last Name *</label>
        <input
            id="last_name"
            type="text"
            name="last_name"
            value="{{ old('last_name', $student->last_name) }}"
            class="form-control"
            required
        >
        <x-form-error field="last_name" />
    </div>

    <div class="form-group">
        <label for="password" class="form-label">
            Account Password {{ $student->exists ? '(Leave blank to keep current)' : '*' }}
        </label>
        <input
            id="password"
            type="password"
            name="password"
            class="form-control"
            {{ $student->exists ? '' : 'required' }}
            autocomplete="new-password"
        >
        <span class="form-help">Minimum 8 characters.</span>
        <x-form-error field="password" />
    </div>

    <div class="form-group">
        <label for="department_id" class="form-label">Department *</label>
        <select id="department_id" name="department_id" class="form-control" required>
            <option value="">Select a department...</option>
            @foreach ($departments as $department)
                <option
                    value="{{ $department->id }}"
                    @selected((string) old('department_id', $student->department_id) === (string) $department->id)
                >
                    {{ $department->name }} ({{ $department->code }})
                </option>
            @endforeach
        </select>
        <x-form-error field="department_id" />
    </div>

    <div class="form-group">
        <label for="year_of_study" class="form-label">Year of Study (1–6) *</label>
        <select id="year_of_study" name="year_of_study" class="form-control" required>
            @for ($year = 1; $year <= 6; $year++)
                <option
                    value="{{ $year }}"
                    @selected((int) old('year_of_study', $student->year_of_study ?? 1) === $year)
                >
                    Year {{ $year }}
                </option>
            @endfor
        </select>
        <x-form-error field="year_of_study" />
    </div>


    <div class="form-group">
        <label for="date_of_birth" class="form-label">Date of Birth</label>
        <input
            id="date_of_birth"
            type="date"
            name="date_of_birth"
            value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
            class="form-control"
        >
        <x-form-error field="date_of_birth" />
    </div>
</div>

<div class="form-group">
    <label for="address" class="form-label">Address</label>
    <textarea
        id="address"
        name="address"
        rows="2"
        class="form-control"
    >{{ old('address', $student->address) }}</textarea>
    <x-form-error field="address" />
</div>

<div class="actions-group">
    <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Student' }}</button>
    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
</div>
