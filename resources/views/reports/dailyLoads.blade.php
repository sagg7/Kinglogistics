<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Daily Loads</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/reports/dailyLoads.min.js') }}"></script>
    @endsection

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <fieldset class="form-group col-4">
                        <label for="dateRange">Graph type</label>
                        {!! Form::select('graphType', ['trips' => 'Per trips', 'shippers' => 'Per customers'], null, ['class' => 'form-control', 'id' => 'graphType']) !!}
                    </fieldset>
                    <fieldset class="form-group col-4">
                        <label for="dateRange">Select Dates</label>
                        <input type="text" id="dateRange" class="form-control">
                    </fieldset>
                    <fieldset class="form-group col-4">
                        <label for="dateRange">Customer</label>
                        {!! Form::select('shipper_id', [], null, ['class' => 'form-control', 'id' => 'shipper_id']) !!}
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
    </div>
</x-app-layout>
