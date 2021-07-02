<x-app-layout>
    <x-slot name="crumb_section">Rate</x-slot>
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
                const nameFormatter = (params) => {
                    if (params.value) {
                        return params.value.name;
                    } else
                        return '';
                }
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Group', field: 'rate_group', valueFormatter: nameFormatter},
                        {headerName: 'Shipper', field: 'shipper', valueFormatter: nameFormatter},
                        {headerName: 'Zone', field: 'zone', valueFormatter: nameFormatter},
                        {headerName: 'Start Mileage', field: 'start_mileage'},
                        {headerName: 'End Mileage', field: 'end_mileage'},
                        {headerName: 'Shipper Rate', field: 'shipper_rate', valueFormatter: moneyFormatter},
                        {headerName: 'Carrier Rate', field: 'carrier_rate', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/rate/edit', icon: 'feather icon-edit'},
                        {route: '/rate/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/rate/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', ['create_btn' => ['url' => '/rate/create', 'text' => 'Create rate']])@endcomponent
</x-app-layout>
