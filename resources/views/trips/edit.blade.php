<x-app-layout>
    <x-slot name="crumb_section">Trip</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('modals')
        @include('loads.common.modals.addLoadType')
        @include('loads.common.modals.deleteLoadType')
        @include('loads.common.modals.addOrigin')
        @include('loads.common.modals.addDestination')
    @endsection

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script src="{{ asset('js/sections/loads/coordsMaps.min.js') }}"></script>
        <script src="{{ asset('js/sections/trips/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            (() => {
                $("#shipper_id")
                    .html(`<option value="{{ $trip->shipper_id }}">{{ $trip->shipper->name }}</option>`)
                    .val({{ $trip->shipper_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['trip.update', $trip->id], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'loadForm']) !!}
    @include('trips.common.form')
    {!! Form::close() !!}
</x-app-layout>