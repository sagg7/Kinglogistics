<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incidents/common.min.js?1.0.1') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            @if(auth()->guard('carrier')->check())
            const carrierId = {{ auth()->user()->id }};
            @endif
            const canvases = [
                @if(!$incident->safety_signature)
                {canvas: document.getElementById('safety_signature'), required: true},
                @endif
                @if(!$incident->driver_signature)
                {canvas: document.getElementById('driver_signature')},
                @endif
            ];
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
        <script src="{{ asset('js/common/initSignature.min.js?1.0.1') }}"></script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => ['incident.update', $incident->id], 'method' => 'post', 'class' => 'form form-vertical with-sig-pad']) !!}
    @include('incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>
