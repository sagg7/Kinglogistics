<x-app-layout>
    <x-slot name="crumb_section">Descriptions</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-photo", "title" => "Photo"])
    @endsection
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
                        {headerName: 'Spanish Name', field: 'name_spanish'},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-load']))
                        {text: 'Edit', route: '/load/description/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-load']))
                        {route: '/load/description/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/load/description/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', auth()->user()->can(['create-load']) ? ['create_btn' => ['url' => '/load/description/create', 'text' => 'Create description']] : [])@endcomponent
</x-app-layout>
