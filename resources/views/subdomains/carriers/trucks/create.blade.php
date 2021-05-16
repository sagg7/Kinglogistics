<x-app-layout>
    <x-slot name="crumb_section">Truck</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'truck.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.trucks.common.form')
    {!! Form::close() !!}
</x-app-layout>
