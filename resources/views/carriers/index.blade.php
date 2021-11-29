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
                        {route: '/carrier/delete', type: 'delete'},
                        /*{text: 'Prospect', route: "/carrier/setStatus", route_params: {status:"prospect"}, icon: 'fas fa-check-circle', type: 'confirm', conditional: 'status === "interested"', menuData: {title: 'Set status as prospect?'}},
                        {text: 'Ready to work', route: "/carrier/setStatus", route_params: {status:"ready"}, icon: 'fas fa-check-circle', type: 'confirm', conditional: 'status === "prospect"', menuData: {title: 'Set status as ready to work?'}},
                        {text: 'Active', route: "/carrier/setStatus", route_params: {status:"active"}, icon: 'fas fa-check-circle', type: 'confirm', conditional: 'status === "ready_to_work" || params.data.status === "not_working"', menuData: {title: 'Set status as active?'}},
                        {text: 'Not working', route: "/carrier/setStatus", route_params: {status:"not_working"}, icon: 'far fa-times-circle', type: 'confirm', conditional: 'status === "active"', menuData: {title: 'Set status as not working?'}},
                        {text: 'Not rehirable', route: "/carrier/setStatus", route_params: {status:"not_rehirable"}, icon: 'fas fa-ban', type: 'confirm', conditional: 'status === "active"', menuData: {title: 'Set status as not rehirable?'}},*/
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
