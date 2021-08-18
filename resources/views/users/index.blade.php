<x-app-layout>
    <x-slot name="crumb_section">Staff</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                let getRole = (params) => {
                    if (params.data)
                        return params.data.roles[0].name;
                };
                tbAG = new tableAG({
                    columns: [
                        //{headerName: 'Fecha', field: 'date'},
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Role', field: 'role', valueFormatter: getRole},
                        {headerName: 'Email', field: 'email'},
                        {headerName: 'Phone', field: 'phone'},
                    ],
                    menu: [
                        {text: 'Edit', route: '/user/edit', icon: 'feather icon-edit'},
                        {route: '/user/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/user/search',
                    tableRef: 'tbAG',
                    successCallback: (res) => {
                        res.rows.forEach((item) => {
                            item.area = item.roles.name;
                        });
                    }
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
