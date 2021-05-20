<x-app-layout>
    <x-slot name="crumb_section">Incident Type</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'incidentType.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incidentTypes.common.form')
    {!! Form::close() !!}
</x-app-layout>
