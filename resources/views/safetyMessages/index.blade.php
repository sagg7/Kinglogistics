<x-app-layout>
    <x-slot name="crumb_section">Messages</x-slot>
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
                        {headerName: 'Title', field: 'title'},
                        //{headerName: 'Preview', field: 'preview'},
                    ],
                    menu: [
                        //{text: 'Show', route: '/safetyMessage/show', icon: 'feather icon-eye'},
                        @if(auth()->user()->can(['update-safety-messages']))
                        {text: 'Edit', route: '/safetyMessage/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-safety-messages']))
                        {route: '/safetyMessage/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/safetyMessage/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
