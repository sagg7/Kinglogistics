<x-app-layout>
    <x-slot name="crumb_section">Charge</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/charges/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'charge.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('charges.common.form')
    {!! Form::close() !!}
</x-app-layout>
