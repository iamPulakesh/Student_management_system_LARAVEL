@props(['field'])

@error($field)
    <div class="field-error" role="alert">{{ $message }}</div>
@enderror
