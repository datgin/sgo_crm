@if ($label)
    <label for="{{ $id }}" class="form-label fw-medium {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>
@endif

<input type="text" name="{{ $name }}" id="{{ $id }}" class="form-control" value="{{ $value }}"
    placeholder="Chọn {{ strtolower($placeholder ?? $label) }}" autocomplete="off">

<small class="text-danger error-message {{ $name }}"></small>

@push('scripts')
    <script>
        $(function() {
            const isSingle = @json($single);
            const input = $('#{{ $id }}');

            input.daterangepicker({
                singleDatePicker: isSingle,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-MM-YYYY',
                    cancelLabel: 'Hủy',
                    applyLabel: 'Áp dụng',
                    customRangeLabel: 'Tùy chọn',
                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    monthNames: [
                        'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                    ],
                    firstDay: 1 // Bắt đầu từ thứ Hai
                }
            });

            input.on('apply.daterangepicker', function(ev, picker) {
                if (isSingle) {
                    $(this)
                        .val(picker.startDate.format('DD-MM-YYYY'))
                        .trigger('input'); // <<== THÊM DÒNG NÀY
                } else {
                    $(this)
                        .val(
                            picker.startDate.format('DD-MM-YYYY') +
                            ' - ' +
                            picker.endDate.format('DD-MM-YYYY')
                        )
                        .trigger('input'); // <<== THÊM DÒNG NÀY
                }
            });

            input.on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('').trigger('input');
            });
        });
    </script>
@endPush
