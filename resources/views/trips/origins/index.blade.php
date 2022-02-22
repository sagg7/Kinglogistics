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
                function CoordsLinkRenderer() {}
                CoordsLinkRenderer.prototype.init = (params) => {
                    this.eGui = document.createElement('div');
                    const coords = params.value;
                    const arr = coords.split(',');
                    const latitude = Number(arr[0]).toFixed(5);
                    const longitude = Number(arr[1]).toFixed(5);
                    this.eGui.innerHTML = `<a href="http://www.google.com/maps/place/${coords}" target="_blank">${latitude},${longitude}</a>`;
                }
                CoordsLinkRenderer.prototype.getGui = () => {
                    return this.eGui;
                }
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Latitude', field: 'coords', cellRenderer: CoordsLinkRenderer},
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
