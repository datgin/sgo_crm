@extends('backend.layouts.app')

@section('content')
    <form>
        <div class="row g-3">
            <div class="col-lg-12">
                <x-input name="employee_id" label="Nhập id nhân viên" />
            </div>

            <div class="col-lg-12">
                <x-textarea name="message" label="Nôi dung tin nhắn" />
            </div>
        </div>

        <button class="btn btn-primary btn-sm mt-3" type="submit">Gửi</button>
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                
                let employeeId = $('input[name="employee_id"]').val()
                let message = $('textarea[name="message"]').val()

                $.ajax({
                    url: '/notifications/send',
                    method: 'POST',
                    data: {
                        employeeId,
                        message
                    },
                    success: (response) => {
                        datgin.success(response.message)
                    },
                    error: (xhr) => {
                        datgin.error(xhr.responseJSON.message)
                    }
                })
            })
        })
    </script>
@endpush
