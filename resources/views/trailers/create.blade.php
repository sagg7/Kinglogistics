<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'trailer.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trailers.common.form')
    {!! Form::close() !!}
</x-app-layout>
