@isset($label)
    <label for="{{ $id }}" class="form-label fw-medium {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>
@endisset

@if ($type !== 'textarea')
    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
        class="form-control {{ $class }}" placeholder="{{ $placeholder }}" value="{{ $value }}"
        @disabled($disabled)>
@else
    <textarea id="{{ $id }}" name="{{ $name }}" class="form-control {{ $class }}"
        placeholder="{{ $placeholder }}" rows="{{ $rows }}" @disabled($disabled)>{!! $value !!}</textarea>
@endif

<small class="text-danger error-message"></small>

@push('styles')
    <style>
        .error-message {
            display: none;
            margin-top: 5px;
            font-weight: bolder;
        }
    </style>
@endpush
