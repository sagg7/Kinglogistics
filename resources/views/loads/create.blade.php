<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('modals')
        @include('loads.common.modals.addLoadType')
        @include('loads.common.modals.deleteLoadType')
        @include('loads.common.modals.addOrigin')
        @include('loads.common.modals.addDestination')
    @endsection

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script src="{{ asset('js/sections/loads/common.min.js?1.0.2') }}"></script>
        <script src="{{ asset('js/sections/loads/coordsMaps.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'load.store', 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'loadForm']) !!}
    @include('loads.common.form')
    {!! Form::close() !!}
</x-app-layout>
