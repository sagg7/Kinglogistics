<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['trailer.update', $trailer->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trailers.common.form')
    {!! Form::close() !!}
</x-app-layout>
