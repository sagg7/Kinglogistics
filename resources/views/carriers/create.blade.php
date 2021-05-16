<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'carrier.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('carriers.common.form')
    {!! Form::close() !!}
</x-app-layout>
