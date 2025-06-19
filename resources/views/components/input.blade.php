@if($label)
    <label for="{{ $id }}" class="form-label fw-medium {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>
@endif

@if ($type !== 'textarea')
    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
        class="form-control {{ $class }}" placeholder="Nhập {{ strtolower($placeholder ?? $label) }}"
        value="{{ $value }}" @disabled($disabled)>
@else
    <textarea id="{{ $id }}" name="{{ $name }}" class="form-control {{ $class }}"
        placeholder="Nhập {{ strtolower($placeholder ?? $label) }}" rows="{{ $rows }}"
        @disabled($disabled)>{!! $value !!}</textarea>
@endif

<small class="text-danger error-message {{ $name }}"></small>
