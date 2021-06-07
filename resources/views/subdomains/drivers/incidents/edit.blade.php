<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            const canvases = [
                {canvas: document.getElementById('driver_signature'), required: true},
            ];
        </script>
        <script src="{{ asset('js/common/initSignature.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => ['incident.update', $incident->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.drivers.incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>
