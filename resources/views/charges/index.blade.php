<x-app-layout>
    <x-slot name="crumb_section">Charge</x-slot>
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
                const upperFormatter = (params) => {
                    return  params.value ? `${params.value.replace(/^\w/, (c) => c.toUpperCase())}` : '';
                };
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                const carriersFormatter = (params) => {
                    if (params.value) {
                        let carriers = '';
                        params.value.forEach((item, idx) => {
                            carriers += `${item.name}`;
                            if (idx > 0)
                                carriers += ',';
                        });
                        return carriers !== '' ? carriers : 'All';
                    } else
                        return '';
                }
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Type', field: 'charge_type', valueFormatter: nameFormatter},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                        {headerName: 'Description', field: 'description'},
                        {headerName: 'Period', field: 'period', valueFormatter: upperFormatter},
                        {headerName: '{{session('renames')->carrier ?? 'Carriers'}}', field: 'carriers', sortable:false, valueFormatter: carriersFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['create-statement']))
                        {text: 'Edit', route: '/charge/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-statement']))
                        {route: '/charge/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/charge/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    @component('components.aggrid-index', auth()->user()->can(['create-carrier']) ? ['create_btn' => ['url' => '/charge/create', 'text' => 'Create Charge']] : [])@endcomponent
</x-app-layout>
