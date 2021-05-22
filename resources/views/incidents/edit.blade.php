<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incidents/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            (() => {
                $("#carrier_id")
                    .html(`<option value="{{ $incident->carrier_id }}">{{ $incident->carrier->name }}</option>`)
                    .val({{ $incident->carrier_id }})
                    .trigger('change');
                $("#driver_id")
                    .html(`<option value="{{ $incident->driver_id }}">{{ $incident->driver->name }}</option>`)
                    .val({{ $incident->driver_id }})
                    .prop('disabled', false)
                    .trigger('change')
                $("#truck_id")
                    .html(`<option value="{{ $incident->truck_id }}">{{ $incident->truck->number }}</option>`)
                    .val({{ $incident->truck_id }})
                    .trigger('change');
                $("#trailer_id")
                    .html(`<option value="{{ $incident->trailer_id }}">{{ $incident->trailer->number }}</option>`)
                    .val({{ $incident->trailer_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => ['incident.update', $incident->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>