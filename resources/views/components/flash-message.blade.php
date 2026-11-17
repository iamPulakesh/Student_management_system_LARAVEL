{{--
    Shows success, error and validation messages.
--}}

@if (session('success'))
    <div class="alert alert-success" role="status">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul style="margin: 0.4rem 0 0; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
