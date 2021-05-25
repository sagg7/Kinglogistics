<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['load.update', $load->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('loads.common.form')
    {!! Form::close() !!}
</x-app-layout>
