<x-app-layout>
    <x-slot name="crumb_section">Zone</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'zone.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('zones.common.form')
    {!! Form::close() !!}
</x-app-layout>
