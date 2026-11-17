{{--
    Student's profile edit form.
--}}

<form method="POST" action="{{ route('student.profile.update') }}" id="profile-edit-mode" style="display: none;">
    @csrf
    @method('PUT')

    <div class="form-grid" style="margin-bottom: 1rem;">
        <div class="form-group">
            <label for="profile_phone">Phone No</label>
            <input
                id="profile_phone"
                type="tel"
                name="phone"
                value="{{ old('phone', $student->phone) }}"
                placeholder="e.g. 9876543210"
            >
            <x-form-error field="phone" />
        </div>

        <div class="form-group">
            <label for="profile_dob">Date of Birth</label>
            <input
                id="profile_dob"
                type="date"
                name="date_of_birth"
                value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                min="{{ now()->subYears(\App\Models\Student::MAX_AGE)->toDateString() }}"
                max="{{ now()->subYears(\App\Models\Student::MIN_AGE)->toDateString() }}"
            >
            <x-form-error field="date_of_birth" />
        </div>

        <div class="form-group">
            <label for="profile_gender">Gender</label>
            <select id="profile_gender" name="gender">
                <option value="">Select Gender...</option>
                @foreach (\App\Models\Student::GENDERS as $gender)
                    <option value="{{ $gender }}" @selected(old('gender', $student->gender) === $gender)>{{ $gender }}</option>
                @endforeach
            </select>
            <x-form-error field="gender" />
        </div>

        <div class="form-group">
            <label for="profile_current_password">Current Password</label>
            <input
                id="profile_current_password"
                type="password"
                name="current_password"
                autocomplete="current-password"
            >
            <span class="form-help">Needed only when setting a new password.</span>
            <x-form-error field="current_password" />
        </div>

        <div class="form-group">
            <label for="profile_password">New Password</label>
            <input
                id="profile_password"
                type="password"
                name="password"
                autocomplete="new-password"
            >
            <span class="form-help">At least 8 characters.</span>
            <x-form-error field="password" />
        </div>

        <div class="form-group">
            <label for="profile_password_confirmation">Confirm New Password</label>
            <input
                id="profile_password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
            >
        </div>
    </div>

    <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="profile_address">Address</label>
        <textarea
            id="profile_address"
            name="address"
            rows="2"
        >{{ old('address', $student->address) }}</textarea>
        <x-form-error field="address" />
    </div>

    <div class="actions-group">
        <button type="submit" class="btn">Save Changes</button>
        <button type="button" class="btn" onclick="toggleProfileEdit(false)">Cancel</button>
    </div>
</form>

<script>
    function toggleProfileEdit(showEdit) {
        const viewMode = document.getElementById('profile-view-mode');
        const editMode = document.getElementById('profile-edit-mode');
        const editBtn = document.getElementById('edit-profile-btn');

        if (showEdit) {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
            editBtn.style.display = 'none';
        } else {
            viewMode.style.display = 'block';
            editMode.style.display = 'none';
            editBtn.style.display = 'inline-flex';
        }
    }

    @if ($errors->any())
        toggleProfileEdit(true);
    @endif
</script>
