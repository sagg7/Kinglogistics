<x-app-layout>
    <x-slot name="crumb_section">Bonus</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/bonuses/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'bonusTypeModal', 'name' => 'Bonus Type', 'route' => 'bonusType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteBonusTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteBonusTypeModal', 'name' => 'Bonus Type', 'route' => 'bonusType.delete', 'selectId' => 'type'])
    @endsection

    {!! Form::open(['route' => 'bonus.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('bonuses.common.form')
    {!! Form::close() !!}
</x-app-layout>
