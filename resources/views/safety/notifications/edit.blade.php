<x-app-layout>
    <x-slot name="crumb_section">Notification</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    {!! Form::open(['route' => ['notification.update', $notification->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('notifications.common.form')
    {!! Form::close() !!}
</x-app-layout>
