<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
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
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                        {headerName: 'Control #', field: 'control_number'},
                        /*{headerName: 'Customer PO', field: 'customer_po'},
                        {headerName: 'Customer Reference', field: 'customer_reference'},*/
                        {headerName: 'Origin', field: 'origin'},
                        {headerName: 'Destination', field: 'destination'},
                    ],
                    menu: [
                        {text: 'Show', route: '/load/show', icon: 'feather icon-eye'},
                        @if(auth()->guard('web')->check())
                        {text: 'Edit', route: '/load/edit', icon: 'feather icon-edit'},
                        {route: '/load/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/load/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
