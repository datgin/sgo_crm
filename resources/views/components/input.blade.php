@if ($label)
    <label for="{{ $id }}" class="form-label fw-medium {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>
@endif

@if ($type === 'password')
    <div class="position-relative">
@endif

<input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
    class="form-control {{ $class }}" placeholder="Nhập {{ strtolower($placeholder ?? $label) }}"
    value="{{ $value }}" @disabled($disabled)>

@if ($type === 'password')
    <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3" toggle="#password" style="cursor: pointer;">
        <i class="far fa-eye"></i>
    </span>
    </div>
@endif

<small class="text-danger error-message {{ $name }}"></small>

