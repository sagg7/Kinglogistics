<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Show</x-slot>

    @section('modals')
        @include('loads.common.modals.addOrigin')
        @include('loads.common.modals.addDestination')
    @endsection

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script>
            const readOnly = true;
        </script>
        <script src="{{ asset('js/sections/loads/common.min.js?1.0.4') }}"></script>
    @endsection

    @include('loads.common.showForm')
</x-app-layout>
