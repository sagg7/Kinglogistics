<x-app-layout>
    <x-slot name="crumb_section">Rate</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/rates/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('rates.common.modals.addRateGroup')
        @include('rates.common.modals.deleteRateGroup')
    @endsection

    {!! Form::open(['route' => 'rate.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('rates.common.form')
    {!! Form::close() !!}
</x-app-layout>
