<x-app-layout>
    <x-slot name="crumb_section">Tracking</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>
        <script>
            const data = @json($data);
            const channel = "{{ $channel }}";
            const echoEvent = "{{ $event }}";
            const company = @json($company);
        </script>
        <script src="{{ asset('js/sections/tracking/common.min.js?1.0.7') }}"></script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div id="map" style="width: 100%; height: calc(100vh - 265px);"></div>
            </div>
        </div>
    </div>
</x-app-layout>
