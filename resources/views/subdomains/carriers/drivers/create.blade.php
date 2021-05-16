<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/subdomains/carriers/drivers/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'driver.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.drivers.common.form')
    {!! Form::close() !!}
</x-app-layout>
