<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/subdomains/carriers/expenses/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'expenseTypeModal', 'name' => 'Expense Type', 'route' => 'carrierExpenseType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteExpenseTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteExpenseTypeModal', 'name' => 'Expense Type', 'route' => 'carrierExpenseType.delete', 'selectId' => 'type')
    @endsection

    {!! Form::open(['route' => 'carrierExpense.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
