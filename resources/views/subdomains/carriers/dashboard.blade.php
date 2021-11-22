<x-app-layout>

    @section("scripts")
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>
        <script>
            const data = @json($tracking["data"]);
            const channel = "{{ $tracking["channel"] }}";
            const echoEvent = "{{ $tracking["event"] }}";
            const company = @json($tracking["company"]);
            const guard = 'carrier';
        </script>
        <script src="{{ asset('js/sections/tracking/common.min.js?1.0.7') }}"></script>
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.6') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection

    @include('dashboard.common.loadStatus')

    <section id="trackingSection">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="card-content">
                        <div id="map" style="width: 100%; height: calc(100vh - 265px);"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>
