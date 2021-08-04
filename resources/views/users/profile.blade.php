<x-app-layout>
    <x-slot name="crumb_section">User</x-slot>
    <x-slot name="crumb_subsection">Profile</x-slot>

    {!! Form::open(['route' => ['user.update', $user->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('users.common.form', ['type' => 'profile'])
    {!! Form::close() !!}
</x-app-layout>
