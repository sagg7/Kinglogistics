<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
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
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Carrier', field: 'carrier'},
                        {headerName: 'Driver', field: 'driver'},
                        {headerName: 'Trailer', field: 'trailer'},
                        {headerName: 'Period', field: 'period'},
                        {headerName: 'Cost', field: 'cost'},
                        {headerName: 'Deposit', field: 'deposit'},
                    ],
                    menu: [
                        {text: 'Edit', route: '/rental/edit', icon: 'feather icon-edit'},
                        {route: '/rental/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/rental/search',
                    tableRef: 'tbAG',
                    successCallback: (res) => {
                        res.rows.forEach((item) => {
                            item.carrier = item.carrier.name;
                            item.driver = item.driver.name;
                            item.trailer = item.trailer.number;
                        });
                    }
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
