<div class="form-grid">
    <div class="form-group">
        <label for="employee_number" class="form-label">Employee Number *</label>
        <input
            id="employee_number"
            type="text"
            name="employee_number"
            value="{{ old('employee_number', $teacher->employee_number) }}"
            class="form-control"
            maxlength="4"
            pattern="[A-Za-z0-9]+"
            title="Maximum 4 alphanumeric characters"
            required
        >
        <x-form-error field="employee_number" />
    </div>

    <div class="form-group">
        <label for="name" class="form-label">Full Name *</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $teacher->user?->name) }}"
            class="form-control"
            required
        >
        <x-form-error field="name" />
    </div>

    <div class="form-group">
        <label for="password" class="form-label">
            Account Password {{ $teacher->exists ? '(Leave blank to keep current)' : '*' }}
        </label>
        <input
            id="password"
            type="password"
            name="password"
            class="form-control"
            {{ $teacher->exists ? '' : 'required' }}
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
                    @selected((string) old('department_id', $teacher->department_id) === (string) $department->id)
                >
                    {{ $department->name }} ({{ $department->code }})
                </option>
            @endforeach
        </select>
        <x-form-error field="department_id" />
    </div>
</div>

<div class="actions-group">
    <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Teacher' }}</button>
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Cancel</a>
</div>
