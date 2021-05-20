<x-app-layout>
    <x-slot name="crumb_section">Incident Type</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['incidentType.update', $incidentType->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incidentTypes.common.form')
    {!! Form::close() !!}
</x-app-layout>
