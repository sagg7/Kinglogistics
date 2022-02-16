<x-app-layout>
    <x-slot name="crumb_section">Origin</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script src="{{ asset('js/sections/trips/common.min.js?1.0.1') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script src="{{ asset('js/sections/trips/location.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'origin.store', 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'coordsForm']) !!}
    @include('trips.common.originDestinationForm')
    {!! Form::close() !!}
</x-app-layout>
