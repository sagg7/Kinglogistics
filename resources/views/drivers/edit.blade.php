<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['driver.update', $driver->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('drivers.common.form')
    {!! Form::close() !!}
</x-app-layout>
