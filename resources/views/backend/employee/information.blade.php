@extends('backend.layouts.app')

@section('content')
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
            width: 100%;
            /* full card width */
            aspect-ratio: 3 / 4;
            /* giữ đúng tỷ lệ 3x4 */
            object-fit: cover;
            /* cắt ảnh không méo */
            object-position: center;
            /* căn giữa */
            border-radius: 4px;
            /* bo góc nhẹ */
            display: block;
            /* loại bỏ khoảng trắng dưới ảnh */

            margin-bottom: 12px;
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

        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            align-items: start;
        }

        .employee-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.08);
            padding: 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .employee-photo {
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .employee-name {
            font-weight: 600;
            margin-top: 8px;
        }

        .employee-title,
        .employee-department {
            font-size: 0.9rem;
            color: #666;
        }

        .details-link {
            color: #007bff;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .details-link:hover {
            text-decoration: underline;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#departmentFilter').on('change', function() {
                let departmentId = $(this).val();
                let url = "{{ route('employees.information') }}";

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        department: departmentId
                    },
                    beforeSend: () => {
                        $('#loadingOverlay').show();
                    },
                    success: function(response) {
                        $('#employeeListWrapper').html(response.html);
                        $('#totalEmployeeCount').val(response.total_count);
                        $('#activeEmployeeCount').val(response.active_count);
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi tải dữ liệu!');
                        $('#loadingOverlay').hide();
                    },
                    complete: () => {
                        $('#loadingOverlay').hide();
                    }
                });
            });
        });
    </script>
@endpush
