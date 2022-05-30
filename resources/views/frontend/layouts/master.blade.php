
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Tevini</title>
    <link href="{{ asset('assets/front/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/front/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/animate.min.css') }}" />
    @yield('css')
</head>

<body>

    
    @include('frontend.inc.header')

    @yield('content')

    @include('frontend.inc.footer')
    <script src="{{ asset('assets/user/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/iconify.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/wow.min.js') }}"></script>
    <script src='{{ asset('assets/front/js/app.js') }}'> </script>
    <script>
        new WOW().init();
    </script>
    @yield('script')

</body>

</html>