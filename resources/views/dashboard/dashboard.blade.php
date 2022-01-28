<x-app-layout>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection

    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            const guard = 'web';
            const loadChannelId = {{ auth()->user()->broker_id }};
            let tbOnCall = null;
            let tbJobs = null;
        </script>
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.11') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection

    @if(auth()->user()->can(['read-load']) || auth()->user()->can(['read-load-dispatch']))
        @include('dashboard.common.loadStatus')
    @endif

    <div class="row">
        @if(auth()->user()->can(['read-staff']))
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>On call personnel</h3>
                </div>
                <div class="card-body">
                    <div class="card-content" style="height: 360px;">
                        <div class="aggrid ag-auto-height total-row ag-theme-material w-100" id="onCallTable" style="height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->can(['read-driver']))
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Driver status</h3>
                </div>
                <div class="card-body">
                    <div class="card-content"  style="height: 360px;">
                        <div id="driversChart"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->can(['read-trailer']))
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Trailers</h3>
                </div>
                <div class="card-body">
                    <div class="card-content"  style="height: 360px;">
                        <div id="trailersChart"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->can(['read-truck']))
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Trucks use</h3>
                </div>
                <div class="card-body">
                    <div class="card-content"  style="height: 360px;">
                        <div id="trucksChart"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->can(['read-job']))
        <div class="col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Jobs summary</h3>
                </div>
                <div class="card-body">
                    <div class="card-content" style="height: 360px;">
                        <div class="aggrid ag-auto-height total-row ag-theme-material w-100" id="jobsTable" style="height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

</x-app-layout>
