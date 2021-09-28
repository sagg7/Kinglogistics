<x-app-layout>
    <x-slot name="crumb_section">Equipment</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/subdomains/carriers/profile/equipment/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'equipment.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.profile.equipment.common.form')
    {!! Form::close() !!}
</x-app-layout>
