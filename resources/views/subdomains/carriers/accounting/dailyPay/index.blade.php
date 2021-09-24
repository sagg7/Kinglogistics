<x-app-layout>
    <x-slot name="crumb_section">Daily Pay</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                        {headerName: 'Subtotal', field: 'gross_amount', valueFormatter: moneyFormatter},
                        {headerName: 'Reductions', field: 'reductions', valueFormatter: moneyFormatter},
                        {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/dailyPay/edit', icon: 'feather icon-edit'},
                    ],
                    container: 'myGrid',
                    url: '/dailyPay/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', ['create_btn' => ['url' => '/dailyPay/create', 'text' => 'Create Daily Pay Request']])@endcomponent
</x-app-layout>
