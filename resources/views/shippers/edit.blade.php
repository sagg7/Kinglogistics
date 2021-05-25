<x-app-layout>
    <x-slot name="crumb_section">Shipper</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['shipper.update', $shipper->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('shippers.common.form')
    {!! Form::close() !!}
</x-app-layout>
