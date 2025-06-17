@if (session()->has('success'))
    <script>
        datgin.success("{{ session('success') }}")
    </script>
@endif
