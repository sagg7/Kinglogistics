<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                    ],
                    menu: [
                        {text: 'Edit', route: '/incident/edit', icon: 'feather icon-edit'},
                        {route: '/incident/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/incident/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
