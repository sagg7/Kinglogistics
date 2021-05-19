<x-app-layout>
    <x-slot name="crumb_section">Trailer type</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'trailerType.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trailerTypes.common.form')
    {!! Form::close() !!}
</x-app-layout>
