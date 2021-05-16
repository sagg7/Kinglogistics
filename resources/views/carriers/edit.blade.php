<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['carrier.update', $carrier->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('carriers.common.form')
    {!! Form::close() !!}
</x-app-layout>
