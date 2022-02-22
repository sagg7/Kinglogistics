<x-app-layout>
    <x-slot name="crumb_section">Customer</x-slot>
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
                        {headerName: 'Email', field: 'email'},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-customer']))
                        {text: 'Edit', route: '/shipper/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-customer']))
                        {route: '/shipper/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/shipper/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection
    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div id="myGrid"></div>
            </div>
        </div>
    </div>
</x-app-layout>
