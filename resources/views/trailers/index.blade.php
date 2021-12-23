<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
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
                const statusFormatter = (params) => {
                    switch (params.value) {
                        case 'available':
                            return 'Available';
                        case 'rented':
                            return 'Rented';
                        case 'oos':
                            return 'Out of service';
                        default:
                            return '';
                    }
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Number', field: 'number'},
                        {headerName: 'Type', field: 'trailer_type', valueFormatter: nameFormatter},
                        {headerName: 'Plate', field: 'plate'},
                        {headerName: 'VIN', field: 'vin'},
                        {headerName: 'Status', field: 'status', valueFormatter: statusFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/trailer/edit', icon: 'feather icon-edit'},
                        {route: '/trailer/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/trailer/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', [
    'menu' => [
        ['url' => '/trailer/downloadXLS', 'text' => 'Download report', 'icon' => 'fas fa-file-excel']
    ]
    ])@endcomponent
</x-app-layout>
