<x-app-layout>
    <x-slot name="crumb_section">Origins</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            let tbAG = null;
            (() => {
                const latitudeFormatter = (params) => {
                    return params.value.split(',')[0];
                }
                const longitudeFormatter = (params) => {
                    return params.value.split(',')[1];
                }
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Latitude', field: 'coords', valueFormatter: latitudeFormatter},
                        {headerName: 'Longitude', field: 'coords', valueFormatter: longitudeFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-job']))
                        {text: 'Edit', route: '/trip/origin/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-job']))
                        {route: '/trip/origin/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/trip/origin/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', auth()->user()->can(['create-job']) ? ['create_btn' => ['url' => '/trip/origin/create', 'text' => 'Create origin']] : [])@endcomponent
</x-app-layout>
