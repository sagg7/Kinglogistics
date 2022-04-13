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
        <link rel="stylesheet" href="{{ asset('css/guest/app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <style>
            body {
                background: url("/images/guest/backgrounds/guest-bg.jpg") no-repeat center center;
                background-size: cover;
            }
            .right-divider-deco {
                position: fixed;
                top: 0;
                right: 6vw;
                height: 100%;
                border-right: 2px solid #263136;
                transition: all 300ms ease;
            }
            .right-divider-deco:after{
                content: "=";
                position: absolute;
                right: calc(-3vw - 4px);
                color: white;
                top: 50%;
                transform: translate(50%, -50%);
                font-size: 80px;
                font-family: serif;
                font-weight: bold;
            }
            .divider-thick-deco {
                position: absolute;
                right: -3px;
                width: 6px;
                background-color: white;
            }
            .divider-top-deco {
                top: 3vw;
                height: 8vw;
            }
            .divider-bottom-deco {
                bottom: 3vw;
                height: 6px;
            }
            footer {
                width: 100%;
                position: fixed;
                bottom: 0;
                margin-bottom: 10vh;
                display: block;
            }
            footer .container {
                padding: 0 50px;
            }
            .triangle-deco {
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                border-top: 5vw solid transparent;
                border-bottom: 5vw solid transparent;
                border-left: 5vw solid white;
                z-index: -1;
                transition: all 300ms ease;
            }
            #footer-images div[class^=flex]:nth-child(2) img {
                margin-left: auto;
            }
        </style>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
        <div class="right-divider-deco hidden sm:block">
            <div class="divider-thick-deco divider-top-deco"></div>
            <div class="divider-thick-deco divider-bottom-deco"></div>
        </div>
        <footer>
            <div class="triangle-deco"></div>
            <div class="container mx-auto">
                <div class="flex" id="footer-images">
                    <div class="flex-1">
                        <img src="/images/guest/logos/improving.png" alt="We are always improving for you." class="">
                    </div>
                    <div class="flex-1">
                        <img src="/images/app/logos/logo-light.png" alt="King Logistic Oil LLC">
                    </div>
                </div>
            </div>
        </footer>
    </body>
    @yield('scripts')
</html>
