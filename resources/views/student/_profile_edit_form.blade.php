<form method="POST" action="{{ route('student.profile.update') }}" id="profile-edit-mode" style="display: none;">
    @csrf
    @method('PUT')

    <div class="form-grid" style="margin-bottom: 1rem;">
        <div class="form-group">
            <label for="profile_phone" class="form-label">Phone No</label>
            <input
                id="profile_phone"
                type="text"
                name="phone"
                value="{{ old('phone', $student->phone) }}"
                class="form-control"
                placeholder="e.g. 9876543210"
            >
            <x-form-error field="phone" />
        </div>

        <div class="form-group">
            <label for="profile_dob" class="form-label">Date of Birth</label>
            <input
                id="profile_dob"
                type="date"
                name="date_of_birth"
                value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                class="form-control"
            >
            <x-form-error field="date_of_birth" />
        </div>

        <div class="form-group">
            <label for="profile_gender" class="form-label">Gender</label>
            <select id="profile_gender" name="gender" class="form-control">
                <option value="">Select Gender...</option>
                <option value="Male" @selected(old('gender', $student->gender) === 'Male')>Male</option>
                <option value="Female" @selected(old('gender', $student->gender) === 'Female')>Female</option>
                <option value="Other" @selected(old('gender', $student->gender) === 'Other')>Other</option>
                <option value="Prefer not to say" @selected(old('gender', $student->gender) === 'Prefer not to say')>Prefer not to say</option>
            </select>
            <x-form-error field="gender" />
        </div>

        <div class="form-group">
            <label for="profile_password" class="form-label">New Password (optional)</label>
            <input
                id="profile_password"
                type="password"
                name="password"
                class="form-control"
                placeholder="Leave blank to keep current"
                autocomplete="new-password"
            >
            <x-form-error field="password" />
        </div>

        <div class="form-group">
            <label for="profile_password_confirmation" class="form-label">Confirm New Password</label>
            <input
                id="profile_password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-control"
                placeholder="Confirm new password"
                autocomplete="new-password"
            >
        </div>
    </div>

    <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="profile_address" class="form-label">Address</label>
        <textarea
            id="profile_address"
            name="address"
            rows="2"
            class="form-control"
            placeholder="Enter your address"
        >{{ old('address', $student->address) }}</textarea>
        <x-form-error field="address" />
    </div>

    <div class="actions-group">
        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleProfileEdit(false)">Cancel</button>
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
