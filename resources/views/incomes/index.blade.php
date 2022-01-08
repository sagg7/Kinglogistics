<x-app-layout>
    <x-slot name="crumb_section">Income</x-slot>
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
                const dateFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Type', field: 'type', valueFormatter: nameFormatter},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-income']))
                        {text: 'Edit', route: '/income/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-income']))
                        {route: '/income/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/income/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', ['create_btn' => ['url' => '/income/create', 'text' => 'Create Income']])@endcomponent
</x-app-layout>
