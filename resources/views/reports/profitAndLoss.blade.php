<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Profit and Loss</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/reports/profitAndLoss.min.js?1.0.0') }}"></script>
    @endsection

    <div class="card">
        <div class="card-header">
            <fieldset class="form-group col-xl-3 col-lg-4 col-md-6 col-12">
                <label for="dateRange">Select Dates</label>
                <input type="text" id="dateRange" class="form-control">
            </fieldset>
        </div>
    </div>

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div id="barChart"></div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div id="pieIncome"></div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div id="pieExpenses"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="dataTable" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
        </div>
    </div>
</x-app-layout>
