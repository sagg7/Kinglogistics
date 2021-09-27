<x-app-layout>
    <x-slot name="crumb_section">Income</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/incomes/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'incomeTypeModal', 'name' => 'Income Type', 'route' => 'incomeType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteIncomeTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteIncomeTypeModal', 'name' => 'Income Type', 'route' => 'incomeType.delete', 'selectId' => 'type'])
    @endsection

    {!! Form::open(['route' => 'income.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incomes.common.form')
    {!! Form::close() !!}
</x-app-layout>
