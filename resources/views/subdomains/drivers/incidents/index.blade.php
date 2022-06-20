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
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const capitalizeNameFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Type', field: 'incident_type', valueFormatter: nameFormatter},
                        {headerName: '{{session('renames')->carrier ?? 'Carrier'}}', field: 'carrier', valueFormatter: nameFormatter},
                        {headerName: 'Safety User', field: 'user', valueFormatter: nameFormatter},
                        {headerName: 'Sanction', field: 'sanction', valueFormatter: capitalizeNameFormatter},
                    ],
                    menu: [
                        {text: 'Signature', route: '/incident/edit', icon: 'feather icon-edit'},
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
