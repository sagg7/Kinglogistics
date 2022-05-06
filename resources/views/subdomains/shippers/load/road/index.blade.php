<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section('head')
        <style>
            .ag-theme-material .ag-header-cell, .ag-theme-material .ag-header-group-cell,
            .ag-theme-material .ag-cell {
                padding-left: 5px;
                padding-right: 5px;
            }
            .ag-header-cell[col-id="status"] {
                display: none;
            }
            #dataTable, .aggrid .ag-header-cell-text {
                font-size: 11px!important;
            }
        </style>
    @endsection

    @section('modals')
        @include("loads.road.modals.requestDetails")
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            var tbAG = null;
        </script>
        <script src="{{ asset('js/sections/loads/road/dispatch/index.min.js?1.0.0') }}"></script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
