<x-app-layout>
    <x-slot name="crumb_section">Truck</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['truck.update', $truck->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.trucks.common.form')
    {!! Form::close() !!}
</x-app-layout>
