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
                const depositFormatter = (params) => {
                    if (params.value ? params.value.active_rental ? params.value.active_rental.deposit_is_paid : false : false) {
                        return numeral(params.value.active_rental.deposit).format('$0,0.00');
                    } else
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
                        {headerName: '{{session('renames')->carrier ?? 'Carrier'}}', field: 'carrier', valueFormatter: driverFormatter},
                        {headerName: 'Trailer', field: 'trailer', valueFormatter: trailerFormatter},
                        {headerName: 'Plate', field: 'plate'},
                        {headerName: 'VIN', field: 'vin'},
                        {headerName: 'Deposit', field: 'driver', filter:false, sortable: false, valueFormatter: depositFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-truck']))
                        {text: 'Edit', route: '/truck/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-truck']))
                        {route: '/truck/delete', type: 'delete'}
                        @endif
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
