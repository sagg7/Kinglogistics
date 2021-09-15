<x-app-layout>
    <x-slot name="crumb_section">Messages</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="{{ asset("js/sections/safetyMessages/common.min.js?1.0.0") }}"></script>
    @endsection

    {!! Form::open(['route' => 'safetyMessage.store', 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'messageForm']) !!}
    @include('safetyMessages.common.form')
    {!! Form::close() !!}
</x-app-layout>
