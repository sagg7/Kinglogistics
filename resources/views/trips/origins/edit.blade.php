<x-app-layout>
    <x-slot name="crumb_section">Origin</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script src="{{ asset('js/sections/trips/location.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => ['origin.update', $origin->id], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'coordsForm']) !!}
    @include('trips.common.originDestinationForm', ['model' => $origin])
    {!! Form::close() !!}
</x-app-layout>
