<x-app-layout>
    <x-slot name="crumb_section">Job</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/trips/common.min.js?1.0.2') }}"></script>
    @endsection

    {!! Form::open(['route' => 'trip.store', 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'loadForm']) !!}
    @include('trips.common.form')
    {!! Form::close() !!}
</x-app-layout>
