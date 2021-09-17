<x-app-layout>
    <x-slot name="crumb_section">Bonus</x-slot>
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
                const carriersFormatter = (params) => {
                    if (params.value) {
                        let carriers = '';
                        params.value.forEach((item, idx) => {
                            carriers += `${item.name}`;
                            if (idx > 0)
                                carriers += ',';
                        });
                        return carriers !== '' ? carriers : 'All';
                    } else
                        return '';
                }
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Type', field: 'bonus_type', valueFormatter: nameFormatter},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                        {headerName: 'Description', field: 'description'},
                        {headerName: 'Carriers', field: 'carriers', sortable:false, valueFormatter: carriersFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/bonus/edit', icon: 'feather icon-edit'},
                        {route: '/bonus/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/bonus/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', ['create_btn' => ['url' => '/bonus/create', 'text' => 'Create Bonus']])@endcomponent
</x-app-layout>
