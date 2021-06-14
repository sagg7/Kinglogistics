<x-app-layout>
    <x-slot name="crumb_section">Notification</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="{{ asset("js/sections/notifications/common.min.js") }}"></script>
    @endsection

    {!! Form::open(['route' => 'notification.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('notifications.common.form')
    {!! Form::close() !!}
</x-app-layout>
