<x-app-layout>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection

    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            const guard = 'web';
            let tbOnCall = null;
            let tbJobs = null;
        </script>
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.7') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection

    @include('dashboard.common.loadStatus')

    <div class="row">
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>On call personnel</h3>
                </div>
                <div class="card-body">
                    <div class="card-content" style="height: 312px;">
                        <div class="aggrid ag-auto-height total-row ag-theme-material w-100" id="onCallTable" style="height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Driver status</h3>
                </div>
                <div class="card-body">
                    <div class="card-content"  style="height: 312px;">
                        <div id="driversChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Trailers use</h3>
                </div>
                <div class="card-body">
                    <div class="card-content"  style="height: 312px;">
                        <div id="trailersChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Jobs summary</h3>
                </div>
                <div class="card-body">
                    <div class="card-content" style="height: 312px;">
                        <div class="aggrid ag-auto-height total-row ag-theme-material w-100" id="jobsTable" style="height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
