<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/vendors.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/charts/apexcharts.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/extensions/tether-theme-arrows.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/extensions/tether.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/extensions/shepherd-theme-default.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/pickers/pickadate/pickadate.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/forms/select/select2.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/animate/animate.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/vendors/css/extensions/sweetalert2.min.css") }}">
        @yield("vendorCSS")
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" href="{{ asset("app-assets/css/bootstrap.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/bootstrap-extended.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/colors.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/components.min.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/themes/dark-layout.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/themes/semi-dark-layout.css") }}">
        <!-- END: Theme CSS-->

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" href="{{ asset("app-assets/css/core/menu/menu-types/vertical-menu.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/core/colors/palette-gradient.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/pages/dashboard-analytics.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/pages/card-analytics.css") }}">
        <link rel="stylesheet" href="{{ asset("app-assets/css/plugins/tour/tour.css") }}">
        <!--<link rel="stylesheet" href="{{ asset("app-assets/css/plugins/forms/validation/form-validation.min.css") }}">-->
        <!-- END: Page CSS-->

        <!-- BEGIN: Custom CSS-->
        <!--<link rel="stylesheet" href="{{ asset("css/modules/daterangepicker/daterangepicker.css") }}">-->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <link rel="stylesheet" href="{{ asset("css/fontawesome.css") }}">
        <link rel="stylesheet" href="{{ asset("css/app.css?1.0.0") }}">
        <!-- END: Custom CSS-->
        @yield("head")
    </head>
    <body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    @include('layouts.header')
    @include('layouts.menu')
    @yield('modals')
    <div class="app-content content">
        <div class="content-wrapper">
            @isset($crumb_section)
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <div class="breadcrumb-wrapper col-12 pl-0">
                                    <ol class="breadcrumb border-0">
                                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                                        <li class="breadcrumb-item">{{ $crumb_section }}</li>
                                        @isset($crumb_subsection)
                                            <li class="breadcrumb-item">{{ $crumb_subsection }}</li>
                                        @endisset
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
            <main>
                <div class="content-body">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    <footer class="footer footer-static footer-light"></footer>
    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset("app-assets/vendors/js/vendors.min.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/pickers/pickadate/picker.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/pickers/pickadate/picker.date.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/pickers/pickadate/picker.time.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/charts/apexcharts.min.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/extensions/tether.min.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/forms/select/select2.full.min.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/extensions/sweetalert2.all.min.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/vendors/js/extensions/polyfill.min.js") }}" type="application/javascript"></script>
    <!-- END: Vendor JS-->

    <!-- BEGIN: Core JS-->
    <script src="{{ asset("app-assets/js/core/app-menu.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/js/core/app.js") }}" type="application/javascript"></script>
    <!-- END: Core JS-->

    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('js/section/master/globalFunctions.js') }}"></script>
    <script src="{{ asset('js/common/master.min.js?1.0.0') }}"></script>
    <script src="{{ asset('js/modules/apexCharts/configVars.js') }}"></script>
    <script src="{{ asset('js/modules/daterangepicker/configVars.js') }}"></script>

    <!-- BEGIN: Scripts JS-->
    <script src="{{ asset("app-assets/js/scripts/components.js") }}" type="application/javascript"></script>
    <script src="{{ asset("app-assets/js/scripts/forms/select/form-select2.min.js") }}"
            type="application/javascript"></script>
    <script src="{{ asset("app-assets/js/scripts/pickers/dateTime/pick-a-datetime.min.js?1.0.0") }}"
            type="application/javascript"></script>
    <!-- END: Scripts JS-->
    @yield("scripts")
    </body>
</html>
