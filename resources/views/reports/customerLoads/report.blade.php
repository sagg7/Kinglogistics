<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Customer Loads</x-slot>

    @section('head')
        <style>
            #carrier-data .modal-dialog, #loads-data .modal-dialog, #driver-data .modal-dialog {
                max-width: 1140px;
                left: 48%;
                transform: translateX(-50%);
            }
        </style>
    @endsection

    @section('modals')
        @include("reports.customerLoads.modals.carrierDataModal")
        @include("reports.customerLoads.modals.loadsDataModal")
        @include("reports.customerLoads.modals.driverDataModal")
    @endsection

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/reports/customerLoads.min.js?1.0.0') }}"></script>
    @endsection

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <fieldset class="form-group col-xl-3 col-lg-4 col-md-4 col-12">
                        {!! Form::label('dateRange', 'Select Dates') !!}
                        {!! Form::text('dateRange', null, ['class' => 'form-control']) !!}
                    </fieldset>
                    <fieldset class="form-group col-xl-3 col-lg-4 col-md-4 col-12">
                        {!! Form::label('shipper', 'Customer') !!}
                        {!! Form::select('shipper', [], null, ['class' => 'form-control']) !!}
                    </fieldset>
                    <fieldset class="form-group col-xl-3 col-lg-4 col-md-4 col-12">
                        {!! Form::label('carrier', 'Carrier') !!}
                        {!! Form::select('carrier', [], null, ['class' => 'form-control']) !!}
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div id="chart"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3>Active drivers</h3>
            <hr>
            <div id="activeGeneralDrivers" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3>Inactive drivers</h3>
            <hr>
            <div id="inactiveGeneralDrivers" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
        </div>
    </div>
</x-app-layout>
