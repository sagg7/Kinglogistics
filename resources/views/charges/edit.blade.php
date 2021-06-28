<x-app-layout>
    <x-slot name="crumb_section">Charge</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/charges/common.min.js') }}"></script>
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

    {!! Form::open(['route' => ['charge.update', $charge->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('charges.common.form')
    {!! Form::close() !!}
</x-app-layout>
