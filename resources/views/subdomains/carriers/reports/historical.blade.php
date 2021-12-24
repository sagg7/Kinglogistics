<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Historical</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/section/reports/appointmentHistory.min.js') }}"></script>
        <script>
            let _aggrid;
        </script>
        <script src="{{ asset('js/sections/subdomains/carriers/reports/historical.min.js?1.0.0') }}"></script>
    @endsection

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <fieldset class="form-group col-12">
                        <label for="dateRange">Select Dates</label>
                        <input type="text" id="dateRange" class="form-control">
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="reportTable" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
