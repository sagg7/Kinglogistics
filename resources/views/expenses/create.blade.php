<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/expenses/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'expenseTypeModal', 'name' => 'Expense Type', 'route' => 'expenseType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteExpenseTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteExpenseTypeModal', 'name' => 'Expense Type', 'route' => 'expenseType.delete', 'selectId' => 'type'])
    @endsection

    {!! Form::open(['route' => 'expense.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
