<div class="form-grid">
    <div class="form-group">
        <label for="student_number">Registration Number *</label>
        <input
            id="student_number"
            type="text"
            name="student_number"
            value="{{ old('student_number', $student->student_number) }}"
            maxlength="8"
            pattern="[A-Za-z0-9]+"
            title="Maximum 8 alphanumeric characters"
            required
        >
        <span class="form-help">Maximum 8 alphanumeric characters. No special characters.</span>
        <x-form-error field="student_number" />
    </div>

    <div class="form-group">
        <label for="first_name">First Name *</label>
        <input
            id="first_name"
            type="text"
            name="first_name"
            value="{{ old('first_name', $student->first_name) }}"
            required
        >
        <x-form-error field="first_name" />
    </div>

    <div class="form-group">
        <label for="last_name">Last Name *</label>
        <input
            id="last_name"
            type="text"
            name="last_name"
            value="{{ old('last_name', $student->last_name) }}"
            required
        >
        <x-form-error field="last_name" />
    </div>

    <div class="form-group">
        <label for="password">
            {{ $student->exists ? 'Reset Password' : 'Password *' }}
        </label>
        <input
            id="password"
            type="password"
            name="password"
            {{ $student->exists ? '' : 'required' }}
            autocomplete="new-password"
        >
        <x-form-error field="password" />
    </div>

    <div class="form-group">
        <label for="email">Email Address *</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $student->user?->email) }}"
            required
        >
        <x-form-error field="email" />
    </div>

    <div class="form-group">
        <label for="department_id">Department *</label>
        <select id="department_id" name="department_id" required>
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
        <label for="year_of_study">Semester *</label>
        <select id="year_of_study" name="year_of_study" required>
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
        <label for="date_of_birth">Date of Birth</label>
        <input
            id="date_of_birth"
            type="date"
            name="date_of_birth"
            value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
            max="{{ now()->toDateString() }}"
        >
        <x-form-error field="date_of_birth" />
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender">
            <option value="">Select Gender...</option>
            <option value="Male" @selected(old('gender', $student->gender) === 'Male')>Male</option>
            <option value="Female" @selected(old('gender', $student->gender) === 'Female')>Female</option>
            <option value="Other" @selected(old('gender', $student->gender) === 'Other')>Other</option>
        </select>
        <x-form-error field="gender" />
    </div>

    <div class="form-group">
        <label for="phone">Phone No</label>
        <input
            id="phone"
            type="text"
            name="phone"
            value="{{ old('phone', $student->phone) }}"
        >
        <x-form-error field="phone" />
    </div>
</div>

<div class="form-group">
    <label for="address">Address</label>
    <textarea
        id="address"
        name="address"
        rows="2"
    >{{ old('address', $student->address) }}</textarea>
    <x-form-error field="address" />
</div>

<div class="actions-group">
    <button type="submit" class="btn">{{ $submitLabel ?? 'Save Student' }}</button>
    <a href="{{ route('admin.students.index') }}" class="btn">Cancel</a>
</div>
