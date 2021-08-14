<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const upperFormatter = (params) => {
                    return  params.value ? `${params.value.replace(/^\w/, (c) => c.toUpperCase())}` : '';
                };
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'created_at'},
                        {headerName: 'Type', field: 'type', valueFormatter: upperFormatter},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/expense/edit', icon: 'feather icon-edit'},
                        {route: '/expense/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/expense/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
