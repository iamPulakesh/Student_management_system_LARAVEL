<div class="form-grid">
    <div class="form-group">
        <label for="employee_number">Employee Code *</label>
        <input
            id="employee_number"
            type="text"
            name="employee_number"
            value="{{ old('employee_number', $teacher->employee_number) }}"
            maxlength="4"
            pattern="[A-Za-z0-9]+"
            title="Maximum 4 alphanumeric characters"
            required
        >
        <x-form-error field="employee_number" />
    </div>

    <div class="form-group">
        <label for="name">Full Name *</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $teacher->user?->name) }}"
            required
        >
        <x-form-error field="name" />
    </div>

    <div class="form-group">
        <label for="email">Email Address *</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $teacher->user?->email) }}"
            required
        >
        <x-form-error field="email" />
    </div>

    <div class="form-group">
        <label for="phone">Phone No</label>
        <input
            id="phone"
            type="text"
            name="phone"
            value="{{ old('phone', $teacher->phone) }}"
        >
        <x-form-error field="phone" />
    </div>

    <div class="form-group">
        <label for="password">
            {{ $teacher->exists ? 'Reset Password' : 'Password *' }}
        </label>
        <input
            id="password"
            type="password"
            name="password"
            {{ $teacher->exists ? '' : 'required' }}
            autocomplete="new-password"
        >
        <x-form-error field="password" />
    </div>

    <div class="form-group">
        <label for="department_id">Department *</label>
        <select id="department_id" name="department_id" required>
            <option value="">Select Department...</option>
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
    <button type="submit" class="btn">{{ $submitLabel ?? 'Save Faculty' }}</button>
    <a href="{{ route('admin.teachers.index') }}" class="btn">Cancel</a>
</div>
