@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <div class="row row-cols-1 row-cols-lg-2 row-cols-xxl-4">
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-2">
                        <div>
                            <p class="mb-0 fs-6">Tổng nhân viên</p>
                        </div>
                        <div class="ms-auto widget-icon-small text-white bg-gradient-purple">
                            <ion-icon name="people-circle-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div>
                            <h4 class="mb-0">{{ $statistics->total_employees }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-2">
                        <div>
                            <p class="mb-0 fs-6">Đang làm việc</p>
                        </div>
                        <div class="ms-auto widget-icon-small text-white bg-gradient-success">
                            <ion-icon name="briefcase-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div>
                            <h4 class="mb-0">{{ $statistics->active_employees }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-2">
                        <div>
                            <p class="mb-0 fs-6">Đã nghỉ việc</p>
                        </div>
                        <div class="ms-auto widget-icon-small text-white bg-gradient-danger">
                            <ion-icon name="close-circle-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div>
                            <h4 class="mb-0">{{ $statistics->resigned_employees }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-2">
                        <div>
                            <p class="mb-0 fs-6">Sinh nhật tháng này</p>
                        </div>
                        <div class="ms-auto widget-icon-small text-white bg-gradient-info">
                            <ion-icon name="gift-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div>
                            <h4 class="mb-0">{{ $statistics->birthday_this_month }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--end row-->

    <div class="row">
        <div class="col-lg-6">
            <div id="chart-container" class="chart-box"></div>
        </div>
        <div class="col-lg-6">
            <div id="department-percentage-pie" class="chart-box"></div>
        </div>
        <div class="col-lg-6">
            <div id="gender-bar-chart" class="chart-box"></div>
        </div>
        <div class="col-lg-6">
            <div id="education-level-chart" class="chart-box"></div>
        </div>
        <div class="col-lg-6">
            <div id="contract-type-chart" class="chart-box"></div>
        </div>
        <div class="col-lg-6">
            <div id="seniority-chart" class="chart-box"></div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .chart-box {
            border: 1px solid #dee2e6;
            /* màu viền nhẹ */
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Trạng thái nhân viên theo phòng ban - Column 3D Grouped
            Highcharts.chart('chart-container', {
                chart: {
                    type: 'column',
                    options3d: {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {
                    text: 'Thống kê trạng thái nhân viên theo phòng ban'
                },
                xAxis: {
                    categories: {!! json_encode($departments) !!},
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Số lượng'
                    }
                },
                tooltip: {
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        depth: 25
                    }
                },
                series: {!! json_encode($series) !!}
            });

            // 2. Tỷ lệ nhân sự mỗi phòng ban - Pie 3D
            Highcharts.chart('department-percentage-pie', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    }
                },
                title: {
                    text: 'Tỷ lệ nhân sự mỗi phòng ban'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        depth: 35,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    name: 'Tỷ lệ',
                    colorByPoint: true,
                    data: {!! json_encode($departmentPercentage) !!}
                }]
            });

            // 3. Cơ cấu giới tính - Column 3D đơn
            const genderData = {!! json_encode($genderStructure) !!};

            Highcharts.chart('gender-bar-chart', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Cơ cấu nhân viên theo giới tính'
                },
                xAxis: {
                    categories: genderData.map(item => item.name),
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Số lượng',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ' người'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        },
                        colorByPoint: true
                    }
                },
                series: [{
                    name: 'Giới tính',
                    data: genderData.map(item => item.y)
                }]
            });

            const educationData = {!! json_encode($educationStructure) !!};

            Highcharts.chart('education-level-chart', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Cơ cấu nhân viên theo trình độ học vấn'
                },
                plotOptions: {
                    pie: {
                        innerSize: '50%',
                        depth: 45,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} người'
                        }
                    }
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} người</b>'
                },
                series: [{
                    name: 'Nhân viên',
                    data: educationData
                }]
            });
            const contractData = {!! json_encode($contractStructure) !!};

            Highcharts.chart('contract-type-chart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Cơ cấu nhân sự đang làm việc theo loại hợp đồng'
                },
                xAxis: {
                    categories: contractData.map(item => item.name),
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Số lượng nhân viên'
                    }
                },
                tooltip: {
                    pointFormat: '<b>{point.y} nhân viên</b>'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        },
                        colorByPoint: true
                    }
                },
                series: [{
                    name: 'Hợp đồng',
                    data: contractData.map(item => item.y)
                }]
            });
            Highcharts.chart('seniority-chart', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Cơ cấu nhân viên theo thâm niên phòng ban'
                },
                xAxis: {
                    categories: {!! json_encode($seniorityDepartments) !!},
                    tickmarkPlacement: 'on',
                    title: {
                        enabled: false
                    }
                },
                yAxis: {
                    title: {
                        text: 'Số lượng nhân viên'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' người'
                },
                plotOptions: {
                    area: {
                        stacking: 'normal',
                        lineColor: '#666666',
                        lineWidth: 1,
                        marker: {
                            lineWidth: 1,
                            lineColor: '#666666'
                        }
                    }
                },
                series: {!! json_encode($senioritySeries) !!}
            });
        });
    </script>
@endpush
