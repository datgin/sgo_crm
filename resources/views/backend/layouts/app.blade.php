<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ fileExists($setting->favicon) }}">

    @include('backend.includes.style')

    <title>Fobia - Bootstrap5 Admin Template</title>
</head>

<body>
    <!--start wrapper-->
    <div class="wrapper">
        <!--start sidebar -->
        <aside class="sidebar-wrapper" data-simplebar="true">
            @include('backend.includes.aside')
        </aside>
        <!--end sidebar -->

        <!--start top header-->
        <header class="top-header">
            @include('backend.includes.header')
        </header>
        <!--end top header-->

        <!-- start page content wrapper-->
        <div class="page-content-wrapper">
            <!-- start page content-->
            <div class="page-content">
                @yield('content')
            </div>
            <!-- end page content-->
        </div>
        <!--end page content wrapper-->

        <!--start footer-->
        <footer class="footer">
            @include('backend.includes.footer')
        </footer>
        <!--end footer-->

        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top">
            <ion-icon name="arrow-up-outline"></ion-icon>
        </a>
        <!--End Back To Top Button-->

        <!--start switcher-->
        {{-- <div class="switcher-body">
            @include('backend.includes.switcher')
        </div> --}}
        <!--end switcher-->

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->
    </div>
    <!--end wrapper-->

    <div id="loadingOverlay">
        <div id="loading"></div>
    </div>
    {{-- @include('backend.checkinout.check') --}}
    @include('backend.includes.script')

    @include('backend.includes.toastr')

</body>

</html>
