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
                        {headerName: 'Customer', field: 'shipper', valueFormatter: nameFormatter},
                        {headerName: 'Zone', field: 'zone', valueFormatter: nameFormatter},
                        {headerName: 'Start Mileage', field: 'start_mileage'},
                        {headerName: 'End Mileage', field: 'end_mileage'},
                        {headerName: 'Customer Rate', field: 'shipper_rate', valueFormatter: moneyFormatter},
                        {headerName: 'Carrier Rate', field: 'carrier_rate', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['edit-rate']))
                        {text: 'Edit', route: '/rate/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-rate']))
                        {route: '/rate/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/rate/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', auth()->user()->can(['create-rate']) ? ['create_btn' => ['url' => '/rate/create', 'text' => 'Create rate']] : [])@endcomponent
</x-app-layout>
