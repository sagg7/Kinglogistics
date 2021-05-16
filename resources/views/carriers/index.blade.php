<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
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
                        {headerName: 'Email', field: 'email'},
                        {headerName: 'Phone', field: 'phone'},
                    ],
                    menu: [
                        {text: 'Edit', route: '/carrier/edit', icon: 'feather icon-edit'},
                        {route: '/carrier/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/carrier/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
