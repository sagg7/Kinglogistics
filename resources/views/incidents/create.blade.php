<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incidents/common.min.js?1.0.1') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            @if(auth()->guard('carrier')->check())
            const carrierId = {{ auth()->user()->id }};
            @endif
            const canvases = [
                @if(auth()->guard('web')->check())
                {canvas: document.getElementById('safety_signature'), required: true},
                @endif
                {canvas: document.getElementById('driver_signature')},
            ];
        </script>
        <script src="{{ asset('js/common/initsignature.min.js?1.0.2') }}"></script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => 'incident.store', 'method' => 'post', 'class' => 'form form-vertical with-sig-pad']) !!}
    @include('incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>
