@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Cấu hình chung']]" />

    <form action="" method="POST" enctype="multipart/form-data" id="myForm">
        @csrf

        <div class="row">
            {{-- Cột trái: Thông tin nhân viên --}}
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input name="company_name" id="company_name" label="Tên công ty"
                                    value="{{ $setting->company_name }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="hotline" id="hotline" label="Hotline" value="{{ $setting->hotline }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="email" id="email" label="Email" value="{{ $setting->email }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="address" id="address" label="Địa chỉ" value="{{ $setting->address }}" />
                            </div>

                            <div class="col-md-12">
                                <x-input name="copyright" id="copyright" label="Trân trang"
                                    value="{{ $setting->copyright }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Avatar + tình trạng + ghi chú --}}
            <div class="col-md-3">

                <x-submit />

                <x-card title="Logo" class="text-center">
                    <x-file name="logo" width="100%" :value="fileExists($setting->logo)" />
                </x-card>

                <x-card title="Icon" class="text-center">
                    <x-file name="favicon" width="100%" :value="fileExists($setting->favicon)" />
                </x-card>

            </div>
        </div>
    </form>

    {{-- <x-media-popup /> --}}
@endsection

@push('scripts')
    <script>
        submitForm("#myForm", function(response) {
            console.log(response);

            datgin.success(response.message)
        });
    </script>
@endpush
