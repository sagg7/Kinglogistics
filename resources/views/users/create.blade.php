<x-app-layout>
    <x-slot name="crumb_section">User</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    {!! Form::open(['route' => 'user.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('users.common.form')
    {!! Form::close() !!}
</x-app-layout>
