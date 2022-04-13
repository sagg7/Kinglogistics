<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/app/logos/icon.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/guest/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/extensions/sweetalert2.min.css") }}">
    <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/extensions/sweetalert2.min.css") }}">
    <style>
        @font-face {
            font-family: SquareOneBold;
            src: url("/css/guest/fonts/SquareOneBold.otf");
        }

        body {
            color: #919191;
        }

        main > .row {
            height: 100vh;
        }

        .main-title {
            font-size: 6em;
            font-family: SquareOneBold;
            color: #53abdf;
        }

        input[type=email], input[type=password] {
            border-radius: 0;
            border-width: 2px;
            border-color: #dbdbdb;
            padding-top: 1em;
            padding-bottom: 1em;
            position: relative;
        }

        input[type=email]:focus, input[type=password]:focus {
            border-color: #dbdbdb;
            box-shadow: none;
        }

        .form-group {
            position: relative;
        }

        .input-focus-after {
            position: absolute;
            z-index: 1;
            left: 2px;
            top: 2px;
            height: calc(100% - 4px);
            background-color: #3da2dc;
            transition: all 200ms ease-in;
            opacity: 0;
        }

        input[type=email]:focus + .input-focus-after, input[type=password]:focus + .input-focus-after {
            width: 4px;
            opacity: 1;
        }

        input[type=submit] {
            background-image: linear-gradient(to right, #3da2dc, #3d6cdc);
            border-radius: 0;
            border: 0;
        }

        #right-logo {
            max-width: 295px;
            position: absolute;
        }

        #right-logo > img:nth-of-type(2) {
            display: none;
        }

        #bg-image {
            position: fixed;
            left: 0;
            top: 0;
            margin-left: calc(50% - 60px);
            z-index: -1;
            height: 100%;
        }

        @media (max-width: 1200px) {
            .main-title {
                font-size: 5em;
            }
        }

        @media (max-width: 992px) {
            .main-title {
                font-size: 5em;
            }

            main > .row > div:nth-of-type(2) {
                display: none;
            }

            #bg-image {
                left: auto;
                right: 0;
                height: 110%;
                margin-left: auto;
            }

            #right-logo > img:nth-of-type(1) {
                display: none;
            }
            #right-logo > img:nth-of-type(2) {
                display: block;
            }
        }

        @media (max-width: 767px) {
            .main-title {
                font-size: 5em;
            }
        }
    </style>
</head>
<body>
<main class="container">
    <div id="right-logo" class="mt-5">
        <img class="img-fluid" src="{{ asset('/images/allstar/logo.png') }}" alt="ALLSTAR">
        <img class="img-fluid" src="{{ asset('/images/allstar/logo3.png') }}" alt="ALLSTAR">
    </div>
    <div class="row align-items-center">
        <div class="col-lg-4 col-md-12 col-12">
            {{ $slot }}
        </div>
        <div class="offset-lg-2 col-lg-6 col-md-0">
            <img class="img-fluid" src="{{ asset('/images/allstar/logo2.png') }}" alt="ALLSTAR">
        </div>
    </div>
    <img id="bg-image" src="{{ asset('/images/guest/backgrounds/allstar.png') }}" alt="Background">
</main>
<!-- Scripts -->
<script src="{{ asset('/js/guest/jquery-3.6.0.min.js') }}" type="application/javascript"></script>
<script src="{{ asset('/js/guest/app.min.js') }}" type="application/javascript"></script>
<script src="{{ asset('/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('/app-assets/js/scripts/extensions/sweet-alerts.min.js') }}"></script>
<script src="{{ asset('/js/section/master/globalFunctions.js') }}" type="application/javascript"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@yield("scripts")
</body>
</html>
