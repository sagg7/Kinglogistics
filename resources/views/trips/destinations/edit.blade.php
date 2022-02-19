<x-app-layout>
    <x-slot name="crumb_section">Destination</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script src="{{ asset('js/sections/trips/location.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => ['destination.update', $destination->id], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'coordsForm']) !!}
    @include('trips.common.originDestinationForm', ['model' => $destination])
    {!! Form::close() !!}
</x-app-layout>
