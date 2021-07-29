<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
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
                const numberFormatter = (params) => {
                    if (params.value)
                        return params.value.number;
                    else
                        return '';
                };
                const periodFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        //{headerName: 'Fecha', field: 'date'},
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                        {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                        {headerName: 'Trailer', field: 'trailer', valueFormatter: numberFormatter},
                        {headerName: 'Period', field: 'period', valueFormatter: periodFormatter},
                        {headerName: 'Cost', field: 'cost', valueFormatter: moneyFormatter},
                        {headerName: 'Deposit', field: 'deposit', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        {text: 'Create Inspection', route: '/inspection/create', icon: 'feather icon-edit'},
                        {text: 'Edit', route: '/rental/edit', icon: 'feather icon-edit'},
                        {route: '/rental/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/rental/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
