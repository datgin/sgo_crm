<script src="{{ asset('assets/backend/js/pace.min.js') }}"></script>

<!-- JS Files-->
<script src="{{ asset('assets/backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/backend/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/backend/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('assets/backend/js/choices.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.all.min.js"></script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<!-- Plugins -->
<script src="{{ asset('assets/backend/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/backend/js/main.js') }}"></script>


<script src="{{ asset('global/js/toastr.js') }}"></script>
<script src="{{ asset('global/js/helpers.js') }}"></script>
<script src="{{ asset('global/js/validate.js') }}"></script>

<script>
    window.Laravel = {
        employeeId: {{ auth('admin')->user()->id }}
    };
</script>

@vite('resources/js/app.js')

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

@stack('scripts')
