<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <!-- FOR SEO -->
    <!-- <meta property='og:title' content='MarinOne soft'/>
    <meta property='og:image' content='./assets/images/link.jpg'/>
    <meta property='og:description' content='DESCRIPTION OF YOUR SITE'/>
    <meta property='og:url' content='URL OF YOUR WEBSITE'/>
    <meta property='og:image:width' content='1200' />
    <meta property='og:image:height' content='627' />
    <meta property="og:type" content='website'/> -->

    <title>Tevini</title>
    <link rel="icon" href="{{ asset('assets/front/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/bootstrap-5.1.3min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/app.css') }}">
    <!-- <link rel="stylesheet" type="text/css" href="./css/slick.css" />
    <link rel="stylesheet" type="text/css" href="./css/slick-theme.css" /> -->
    @yield('css')
</head>

<body>
    <!-- oncontextmenu="return false;" -->



            {{-- sidebar start here  --}}

            @include('frontend.inc.header')
        {{-- sidebar close here  --}}




            {{-- dashbord content start here  --}}
            @yield('content')
            {{-- dashbord content close here  --}}


            {{-- footer start here  --}}

            @include('frontend.inc.footer')
        {{-- footer close here  --}}





    <script src="{{ asset('assets/user/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/bootstrap-5.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/iconify.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/app.js') }}"></script>
    @yield('script')
</body>

</html>
