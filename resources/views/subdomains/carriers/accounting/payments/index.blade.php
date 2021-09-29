<x-app-layout>
    <x-slot name="crumb_section">Payments</x-slot>
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
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Subtotal', field: 'gross_amount', valueFormatter: moneyFormatter},
                        {headerName: 'Reductions', field: 'reductions', valueFormatter: moneyFormatter},
                        {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        {text: 'PDF', route: '/carrier/payment/downloadPDF', icon: 'fas fa-file-pdf'},
                    ],
                    container: 'myGrid',
                    url: '/payment/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
