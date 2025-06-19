@if ($label)
    <label for="{{ $id }}" class="form-label fw-medium {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>
@endif
<select name="{{ $multiple ? $name . '[]' : $name }}" id="{{ $id }}" class="form-control"
    {{ $multiple ? 'multiple' : '' }}>
    @unless ($multiple)
        <option value="">-- Chọn {{ strtolower($placeholder ?? $label) }} --</option>
    @endunless
    @foreach ($options as $key => $text)
        <option value="{{ $key }}"
            @if ($multiple && is_array($value) && in_array($key, $value)) selected
            @elseif (!$multiple && $value == $key) selected @endif>
            {{ $text }}
        </option>
    @endforeach
</select>

<small class="text-danger error-message {{ $name }}"></small>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('{{ $id }}');
            if (element) {
                new Choices(element, {
                    removeItemButton: {{ $multiple ? 'true' : 'false' }},
                    placeholder: true,
                    placeholderValue: 'Chọn {{ strtolower($placeholder ?? $label) }}',
                    shouldSort: false,
                    searchEnabled: true
                });
            }
        });
    </script>
@endpush
