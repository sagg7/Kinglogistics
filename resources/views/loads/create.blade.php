<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'load.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('loads.common.form')
    {!! Form::close() !!}
</x-app-layout>
