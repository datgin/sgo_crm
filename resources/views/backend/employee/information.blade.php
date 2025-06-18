@extends('backend.layouts.app')

@section('content')
    <div class="container mb-4">
        <div class="custom-container mb-4">
            <div class="row align-items-center">
                <div class="col-12">
                    <h5 class="title-text mb-3">THÔNG TIN NHÂN SỰ CHI TIẾT</h5>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Chọn tìm kiếm theo phòng ban:</label>
                    <select class="form-select" id="departmentFilter">
                        <option value="">Tất cả</option>
                        @forelse ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tổng số nhân sự:</label>
                    <input type="text" id="totalEmployeeCount" class="form-control" value="{{ $employees->count() }}"
                        readonly>
                </div>
                <div class="col-md-3 ">
                    <label class="form-label">Nhân sự đang làm:</label>
                    <input type="text" id="activeEmployeeCount" class="form-control" value="{{ $activeEmployeeCount }}"
                        readonly>
                </div>
            </div>
        </div>

        <div class="row g-3" id="employeeListWrapper">
            @include('backend.employee.partials.employee_list')
        </div>
    </div>
    <div id="loadingOverlay" class="text-center my-3" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .employee-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            height: 100%;
        }

        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .employee-photo {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .employee-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }

        .employee-title {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .employee-department {
            color: #888;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .status-working {
            color: #28a745;
        }

        .status-leave {
            color: #dc3545;
        }

        .details-link {
            color: #007bff;
            text-decoration: none;
            font-size: 13px;
        }

        .details-link:hover {
            text-decoration: underline;
        }

        .container-fluid {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .page-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
    </style>
    <style>
        #loadingOverlay {
            position: fixed;
            top: 50%;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
        }

        .overlay-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#departmentFilter').on('change', function() {
                let departmentId = $(this).val();
                let url = "{{ route('employees.information') }}";

                // Hiện overlay ngay
                $('#loadingOverlay').fadeIn(100);
                let startTime = Date.now();

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        department: departmentId
                    },
                    success: function(response) {
                        let duration = Date.now() - startTime;
                        let delay = Math.max(0, 1000 - duration); // đảm bảo quay ít nhất 1s

                        // Delay xử lý success để spinner quay trước
                        setTimeout(function() {
                            $('#employeeListWrapper').html(response.html);
                            $('#totalEmployeeCount').val(response.total_count);
                            $('#activeEmployeeCount').val(response.active_count);

                            $('#loadingOverlay').fadeOut(
                                200); // tắt overlay sau khi xử lý xong
                        }, delay);
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi tải dữ liệu!');
                        $('#loadingOverlay').fadeOut(200); // tắt overlay nếu lỗi
                    }
                });
            });
        });
    </script>
@endpush
