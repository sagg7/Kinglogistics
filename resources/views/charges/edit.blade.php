<x-app-layout>
    <x-slot name="crumb_section">Charge</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/charges/common.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            (() => {
                const carriers = @json($charge->carriers);
                let carriersHtml = '',
                    carriersIds = [];
                carriers.forEach((item) => {
                    carriersHtml += `<option value="${item.id}">${item.name}</option>`;
                    carriersIds.push(item.id);
                });
                $('[name="carriers[]"]')
                    .html(carriersHtml)
                    .val(carriersIds)
                    .trigger('change');
            })();
        </script>
    @endsection

    @section('modals')
        @include('common.modals.typeModal', ['id' => 'chargeTypeModal', 'name' => 'Charge Type', 'route' => 'chargeType.store', 'selectId' => 'type', 'deleteTypeModalId' => 'deleteChargeTypeModal'])
        @include('common.modals.deleteTypeModal', ['id' => 'deleteChargeTypeModal', 'name' => 'Charge Type', 'route' => 'chargeType.delete', 'selectId' => 'type'])
    @endsection

    {!! Form::open(['route' => ['charge.update', $charge->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('charges.common.form')
    {!! Form::close() !!}
</x-app-layout>
