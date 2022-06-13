<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-photo", "title" => "Photo"])
    @endsection
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
                        {headerName: session['control_number'] ?? 'Control #', field: 'control_number'},
                        {headerName: 'Origin', field: 'origin'},
                        {headerName: 'Destination', field: 'destination'},
                    ],
                    menu: [
                        {text: 'Show', route: '/load/show', icon: 'feather icon-eye'},
                        @if(auth()->user()->can(['update-load']))
                        {text: 'Edit', route: '/load/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-load']))
                        {route: '/load/delete', type: 'delete'}
                        @endif
                    ],
                    gridOptions: {
                        undoRedoCellEditing: true,
                    },
                    container: 'myGrid',
                    url: '/load/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
