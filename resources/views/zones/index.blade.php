<x-app-layout>
    <x-slot name="crumb_section">Zone</x-slot>
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
                        @if(auth()->user()->can(['update-zone']))
                        {text: 'Edit', route: '/zone/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-zone']))
                        {route: '/zone/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/zone/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
