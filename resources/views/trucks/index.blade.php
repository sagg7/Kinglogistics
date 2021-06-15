<x-app-layout>
    <x-slot name="crumb_section">Truck</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const driverFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const trailerFormatter = (params) => {
                    if (params.value)
                        return params.value.number;
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Number', field: 'number'},
                        {headerName: 'Driver', field: 'driver', valueFormatter: driverFormatter},
                        {headerName: 'Trailer', field: 'trailer', valueFormatter: trailerFormatter},
                        {headerName: 'Plate', field: 'plate'},
                        {headerName: 'VIN', field: 'vin'},
                    ],
                    menu: [
                        {text: 'Edit', route: '/truck/edit', icon: 'feather icon-edit'},
                        {route: '/truck/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/truck/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
