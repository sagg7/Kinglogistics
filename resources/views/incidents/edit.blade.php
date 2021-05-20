<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incidents/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => ['incident.update', $incidentType->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>
