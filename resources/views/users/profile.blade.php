<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    {!! Form::open(['route' => ['user.update', $user->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('users.common.form', ['type' => 'profile'])
    {!! Form::close() !!}
</x-app-layout>
