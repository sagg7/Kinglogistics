<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/expenses/common.min.js?1.0.1') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => ['expense.update', $expense->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
