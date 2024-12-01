<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('assets/bootstrap-5.3.3-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="{{ asset('assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/xlsxjs/xlsx.full.min.js') }}"></script>
</head>
<body>
    @yield('content')
</body>
</html>
