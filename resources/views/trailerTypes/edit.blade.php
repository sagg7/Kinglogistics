<x-app-layout>
    <x-slot name="crumb_section">Trailer type</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['trailerType.update', $trailerType->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trailerTypes.common.form')
    {!! Form::close() !!}
</x-app-layout>
