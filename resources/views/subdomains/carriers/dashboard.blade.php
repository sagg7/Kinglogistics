<x-app-layout>
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <!--<script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>-->
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            const data = @json($tracking["data"]);
            const channel = "{{ $tracking["channel"] }}";
            const echoEvent = "{{ $tracking["event"] }}";
            const company = @json($tracking["company"]);
            const guard = 'carrier';
            const loadChannelId = userId;
        </script>
        <!--<script src="{{ asset('js/sections/tracking/common.min.js?1.0.7') }}"></script>-->
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.11') }}"></script>
        <script src="{{ asset('js/sections/carriers/show.min.js?1.0.0') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection

    @include('carriers.common.carrierCards.income&Ranking')
    @include('dashboard.common.loadStatus')
    @include('carriers.common.carrierCards.alerts&Loads')
    @include('carriers.common.carrierCards.tables&Incidents')

    <!--<section id="trackingSection">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-content">
                        <div id="map" style="width: 100%; height: calc(100vh - 265px);"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>-->

</x-app-layout>
