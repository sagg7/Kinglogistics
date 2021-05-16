<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const trailerFormatter = (params) => {
                    if (params.value)
                        return params.value.number;
                    else
                        return '';
                };
                const truckFormatter = (params) => {
                    if (params.value)
                        return params.value.number;
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Trailer', field: 'trailer', valueFormatter: trailerFormatter},
                        {headerName: 'Truck', field: 'truck', valueFormatter: truckFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/driver/edit', icon: 'feather icon-edit'},
                        {route: '/driver/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/driver/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
