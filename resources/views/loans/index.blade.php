<x-app-layout>
    <x-slot name="crumb_section">Loan</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                const percentageFormatter = (params) => {
                    if (params.value)
                        return Number(params.value) + '%';
                    else
                        return '0%';
                };
                const carrierFormatter = (params) => {
                    if (params.value) {
                        return params.value.name;
                    } else
                        return '';
                }
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Carrier', field: 'carrier', valueFormatter: carrierFormatter},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                        {headerName: 'Paid', field: 'paid_amount', valueFormatter: moneyFormatter},
                        {headerName: 'Installments', field: 'installments'},
                        {headerName: 'Paid Installments', field: 'paid_installments'},
                        {headerName: 'Fee', field: 'fee_percentage', valueFormatter: percentageFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['create-loan']))
                        {text: 'Edit', route: '/loan/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-loan']))
                        {route: '/loan/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/loan/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', auth()->user()->can(['create-load']) ? ['create_btn' => ['url' => '/loan/create', 'text' => 'Create Loan']] : [])@endcomponent
</x-app-layout>
