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
                const typeFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Number', field: 'number'},
                        {headerName: 'Type', field: 'trailer_type', valueFormatter: typeFormatter },
                        {headerName: 'Plate', field: 'plate'},
                        {headerName: 'VIN', field: 'vin'},
                        {headerName: 'Status', field: 'status'},
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

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
