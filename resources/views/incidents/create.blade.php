<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incidents/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            const canvases = [
                {canvas: document.getElementById('safety_signature'), required: true},
                {canvas: document.getElementById('driver_signature')},
            ];
        </script>
        <script src="{{ asset('js/common/initSignature.min.js?1.0.0') }}"></script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => 'incident.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>
