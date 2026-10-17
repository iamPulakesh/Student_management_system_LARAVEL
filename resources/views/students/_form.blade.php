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
            {{ $student->exists ? 'Reset Password' : 'Password *' }}
        </label>
        <input
            id="password"
            type="password"
            name="password"
            class="form-control"
            {{ $student->exists ? '' : 'required' }}
            autocomplete="new-password"
        >
        <x-form-error field="password" />
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Email Address *</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $student->user?->email) }}"
            class="form-control"
            required
            placeholder="e.g. student@example.com"
        >
        <x-form-error field="email" />
    </div>

    <div class="form-group">
        <label for="department_id" class="form-label">Department *</label>
        <select id="department_id" name="department_id" class="form-control" required>
            <option value="">Select Department...</option>
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
        <label for="year_of_study" class="form-label">Semester *</label>
        <select id="year_of_study" name="year_of_study" class="form-control" required>
            @for ($sem = 1; $sem <= 8; $sem++)
                <option
                    value="{{ $sem }}"
                    @selected((int) old('year_of_study', $student->year_of_study ?? 1) === $sem)
                >
                    Semester {{ $sem }}
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
            max="{{ now()->toDateString() }}"
            class="form-control"
        >
        <x-form-error field="date_of_birth" />
    </div>

    <div class="form-group">
        <label for="gender" class="form-label">Gender</label>
        <select id="gender" name="gender" class="form-control">
            <option value="">Select Gender...</option>
            <option value="Male" @selected(old('gender', $student->gender) === 'Male')>Male</option>
            <option value="Female" @selected(old('gender', $student->gender) === 'Female')>Female</option>
            <option value="Other" @selected(old('gender', $student->gender) === 'Other')>Other</option>
            <option value="Prefer not to say" @selected(old('gender', $student->gender) === 'Prefer not to say')>Prefer not to say</option>
        </select>
        <x-form-error field="gender" />
    </div>

    <div class="form-group">
        <label for="phone" class="form-label">Phone No</label>
        <input
            id="phone"
            type="text"
            name="phone"
            value="{{ old('phone', $student->phone) }}"
            class="form-control"
            placeholder="e.g. 9876543210"
        >
        <x-form-error field="phone" />
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
