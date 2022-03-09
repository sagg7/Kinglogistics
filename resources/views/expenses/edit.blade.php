<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/expenses/common.min.js?1.0.2') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'expenseTypeModal', 'name' => 'Expense Type', 'route' => 'expenseType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteExpenseTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteExpenseTypeModal', 'name' => 'Expense Type', 'route' => 'expenseType.delete', 'selectId' => 'type'])
        @include('common.modals.typeModal', ['id' => 'expenseAccountModal', 'name' => 'Account', 'route' => 'expenseAccount.store', 'selectId' => 'account', 'deleteTypeModalId' => 'deleteExpenseAccountModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteExpenseAccountModal', 'name' => 'Account', 'route' => 'expenseAccount.delete', 'selectId' => 'account', 'deleteSelectId' => 'delete_account'])
    @endsection

    {!! Form::open(['route' => ['expense.update', $expense->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
