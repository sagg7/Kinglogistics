<x-app-layout>
    <x-slot name="crumb_section">Income</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incomes/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => ['income.update', $income->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incomes.common.form')
    {!! Form::close() !!}
</x-app-layout>
