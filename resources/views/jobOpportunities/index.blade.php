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
                    @if(auth()->guard('web')->check())
                    menu: [
                        {text: 'Show', route: '/jobOpportunity/show', icon: 'feather icon-eye'},
                        @if(auth()->user()->can(['update-job-opportunity']))
                        {text: 'Edit', route: '/jobOpportunity/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-job-opportunity']))
                        {route: '/jobOpportunity/delete', type: 'delete'}
                        @endif
                    ],
                    @endif
                    container: 'myGrid',
                    url: '/jobOpportunity/search',
                    tableRef: 'tbAG',
                    @if(auth()->guard('carrier')->check())
                    gridOptions: {
                        onRowClicked: params => {
                            window.location = `/jobOpportunity/show/${params.data.id}`;
                        },
                        rowClass: 'cursor-pointer',
                    },
                    @endif
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
