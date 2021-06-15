<x-app-layout>
    <x-slot name="crumb_section">Trip</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const zoneFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Zone', field: 'zone', valueFormatter: zoneFormatter},
                    ],
                    menu: [
                        {text: 'Edit', route: '/trip/edit', icon: 'feather icon-edit'},
                        {route: '/trip/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/trip/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
