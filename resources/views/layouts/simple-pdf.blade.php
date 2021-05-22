<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Violation Report Form</title>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
        <script src="{{ asset('js/bootstrap.js') }}"></script>
        <style>
            body {
                font-family: sans-serif;
            }

            table.w-33 td {
                width: 33.33%;
            }

            td, th {
                padding-right: .3em;
                padding-left: .3em;
            }

            th {
                background-color: #000;
                color: #fff;
            }

            h1 {
                font-size: 32px;
            }
        </style>
    </head>
    <body>
    <main>
        {{ $slot }}
    </main>
    </body>
</html>
