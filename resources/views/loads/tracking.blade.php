<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Tracking</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>
        <script src="{{ asset('js/modules/laravel-echo/echo.js') }}"></script>
        <script>
            const data = @json($data);
            const channel = "{{ $channel }}";
            const echoEvent = "{{ $event }}";
        </script>
        <script src="{{ asset('js/sections/tracking/common.min.js') }}"></script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div id="map" style="width: 100%; height: calc(100vh - 265px);"></div>
            </div>
        </div>
    </div>
</x-app-layout>
