<x-app-layout>
    <x-slot name="crumb_section">Job Opportunity</x-slot>
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
                        //{text: 'Show', route: '/jobOpportunity/show', icon: 'feather icon-eye'},
                        {text: 'Edit', route: '/jobOpportunity/edit', icon: 'feather icon-edit'},
                        {route: '/jobOpportunity/delete', type: 'delete'}
                    ],
                    container: 'myGrid',
                    url: '/jobOpportunity/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
