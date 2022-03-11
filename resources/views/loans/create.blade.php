<x-app-layout>
    <x-slot name="crumb_section">Loan</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/loans/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'loan.store', 'method' => 'post', 'class' => 'form form-vertical','enctype' => 'multipart/form-data']) !!}
    @include('loans.common.form')
    {!! Form::close() !!}
</x-app-layout>
