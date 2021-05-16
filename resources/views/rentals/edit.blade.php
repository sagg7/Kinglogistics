<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['rental.update', $user->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('rentals.common.form')
    {!! Form::close() !!}
</x-app-layout>
