<div class="form-grid">
    <div class="form-group">
        <label for="name" class="form-label">Department Name *</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $department->name) }}"
            class="form-control"
            required
        >
        <x-form-error field="name" />
    </div>

    <div class="form-group">
        <label for="code" class="form-label">Department Code (e.g. CSE) *</label>
        <input
            id="code"
            type="text"
            name="code"
            value="{{ old('code', $department->code) }}"
            class="form-control"
            required
        >
        <x-form-error field="code" />
    </div>
</div>

<div class="actions-group">
    <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Department' }}</button>
    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
</div>
