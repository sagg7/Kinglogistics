<x-app-layout>
    <x-slot name="crumb_section">Paperwork</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/paperwork/common.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('paperwork.common.modals.infoModal')
    @endsection
    {!! Form::open(['route' => ['paperwork.update', $paperwork->id], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data']) !!}
    @include('paperwork.common.form')
    {!! Form::close() !!}
</x-app-layout>
