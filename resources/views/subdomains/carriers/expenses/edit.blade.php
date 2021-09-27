<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/subdomains/carriers/expenses/common.min.js?1.0.1') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            (() => {
                @if($expense->truck_id)
                $("#truck_id")
                    .html(`<option value="{{ $expense->truck_id }}">{{ $expense->truck->number }}</option>`)
                    .val({{ $expense->truck_id }})
                    .trigger('change');
                @endif
            })();
        </script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'expenseTypeModal', 'name' => 'Expense Type', 'route' => 'carrierExpenseType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteExpenseTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteExpenseTypeModal', 'name' => 'Expense Type', 'route' => 'carrierExpenseType.delete', 'selectId' => 'type'])
    @endsection

    {!! Form::open(['route' => ['carrierExpense.update', $expense->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
