<x-app-layout>
    <x-slot name="crumb_section">Customer</x-slot>
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
                    ],
                    menu: [
                        {text: 'Edit', route: '/shipper/edit', icon: 'feather icon-edit'},
                        {route: '/shipper/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/shipper/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
