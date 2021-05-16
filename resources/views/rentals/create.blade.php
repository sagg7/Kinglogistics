<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/rentals/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'rental.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('rentals.common.form')
    {!! Form::close() !!}
</x-app-layout>
