{{--
    Shared department form fields.
--}}

<div class="form-grid">
    <div class="form-group">
        <label for="name">Department Name <span class="required">*</span></label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $department->name) }}"
            required
        >
        <x-form-error field="name" />
    </div>

    <div class="form-group">
        <label for="code">Department Code (e.g. CSE) <span class="required">*</span></label>
        <input
            id="code"
            type="text"
            name="code"
            value="{{ old('code', $department->code) }}"
            required
        >
        <x-form-error field="code" />
    </div>
</div>

<div class="actions-group">
    <button type="submit" class="btn">{{ $submitLabel ?? 'Save Department' }}</button>
    <a href="{{ route('admin.departments.index') }}" class="btn">Cancel</a>
</div>
