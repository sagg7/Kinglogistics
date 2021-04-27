<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'King Logistics') }}</title>
        <!-- Favicon -->
        <link href="{{ asset('assets') }}/img/brand/favicon.png" rel="icon" type="image/png">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Extra details for Live View on GitHub Pages -->

        <!-- Icons -->
        <link type="text/css" href="{{ asset('assets') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <link type="text/css" href="{{ asset('assets') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <!-- Argon CSS -->
        <link type="text/css" href="{{ asset('assets') }}/css/argon.css?v=1.2.0" rel="stylesheet">
        <!--jquery confirm-->
        <link type="text/css" href="{{ asset('assets') }}/css/jquery-confirm.min.css" rel="stylesheet">

        @stack('css')
    </head>
    <body class="{{ $class ?? '' }}">
    <div id="loading" class="hidden">
        <div class="loader"></div>
    </div>
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @include('layouts.navbars.sidebar')
        @endauth

        <div class="main-content">
            @include('layouts.navbars.navbar')
            @yield('content')
        </div>

        @guest()
            @include('layouts.footers.guest')
        @endguest

        <script src="{{ asset('assets') }}/vendor/jquery/dist/jquery.min.js"></script>
        <script src="{{ asset('assets') }}/js/jquery-confirm.min.js"></script>
        <script src="{{ asset('assets') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('assets') }}/vendor/js-cookie/js.cookie.js"></script>

        @stack('js')

        <!-- Argon JS -->
        <script src="{{ asset('assets') }}/js/argon.js?v=1.2.0"></script>
        <script src="{{ asset('assets') }}/js/components/vendor/bootstrap-datepicker.js"></script>

    </body>
</html>
