<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    {!! Form::open(['route' => ['shipper.profile.update', $shipper->id, 1], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('shippers.common.form')
    {!! Form::close() !!}
</x-app-layout>
