<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Debt Store</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/redhorseicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom-datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-modified.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css"/>

    <link rel="stylesheet" href="{{ asset("css/magnific-popup.css") }}">

    {{-- IZI TOAS --}}
    <link rel="stylesheet" href="{{ asset("css/iziToast.min.css") }}">

    @yield('styles')
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        @yield('content')
    </div>
    <script src="{{ asset('libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    <script src="{{ asset('libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("js/dataTables.responsive.min.js") }}"></script>
    <script src="{{ asset("js/sweetalert2@11.js") }}"></script>

    <script src="{{ asset("js/jquery.magnific-popup.min.js") }}"></script>

    {{-- IZI TOAST --}}
    <script src="{{ asset("js/iziToast.min.js") }}"></script>
    @yield('scripts')
</body>

</html>
