<x-app-layout>
    <x-slot name="crumb_section">Truck</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/trucks/common.min.js?1.0.0') }}"></script>
    @endsection

    {!! Form::open(['route' => 'truck.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trucks.common.form')
    {!! Form::close() !!}
</x-app-layout>
