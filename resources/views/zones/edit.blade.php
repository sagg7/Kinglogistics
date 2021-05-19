<x-app-layout>
    <x-slot name="crumb_section">Zone</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['zone.update', $zone->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('zones.common.form')
    {!! Form::close() !!}
</x-app-layout>
