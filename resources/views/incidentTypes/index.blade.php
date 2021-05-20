<x-app-layout>
    <x-slot name="crumb_section">Incident Type</x-slot>
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
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Fine', field: 'fine', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/incidentType/edit', icon: 'feather icon-edit'},
                        {route: '/incidentType/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/incidentType/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
