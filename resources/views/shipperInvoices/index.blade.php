<x-app-layout>
    <x-slot name="crumb_section">Shipper</x-slot>
    <x-slot name="crumb_subsection">Invoices</x-slot>

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-expenses", "title" => "Charges List"])
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var penChargesTable,
                comChargesTable,
                penInvoicesTable,
                comInvoicesTable;
            (() => {
                const moneyFormatter = (params) => {
                    if (params.value) {
                        if (params.data.status && params.data.status === 'charges' && params.colDef.field === 'total')
                            return numeral(-params.value).format('$0,0.00');
                        else
                            return numeral(params.value).format('$0,0.00');
                    } else
                        return '$0.00';
                };
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const invoicesColumns = [
                    {headerName: 'Date', field: 'date'},
                    {headerName: 'Shipper', field: 'shipper', valueFormatter: nameFormatter},
                    {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                ];
                penInvoicesTable = new tableAG({
                    columns: invoicesColumns,
                    menu: [
                        {text: 'PDF', route: '/shipper/invoice/downloadPDF', icon: 'fas fa-file-pdf'},
                        {text: 'Complete', route: '/shipper/invoice/complete', type: 'confirm', icon: 'fas fa-check-circle', menuData: {title: 'Set status as a completed invoice?'}},
                    ],
                    container: 'pendingInvoicesGrid',
                    url: '/shipper/invoice/search/pending',
                    tableRef: 'penInvoicesTable',
                });
                let invoices = [];
                $('.nav-pills .nav-link').click((e) => {
                    const link = $(e.currentTarget),
                        href = link.attr('href');
                    switch (href) {
                        case '#pending-invoices':
                            break;
                        case '#completed-invoices':
                            if (!comInvoicesTable)
                                comInvoicesTable = new tableAG({
                                    columns: invoicesColumns,
                                    menu: [
                                        {text: 'PDF', route: '/shipper/invoice/downloadPDF', icon: 'fas fa-file-pdf'},
                                    ],
                                    container: 'completedInvoicesGrid',
                                    url: '/shipper/invoice/search/completed',
                                    tableRef: 'comInvoicesTable',
                                });
                            break;
                    }
                });
            })();
        </script>
    @endsection
    @component('components.nav-pills-form', ['pills' => [['name' => 'Pending Invoices', 'pane' => 'pending-invoices'],['name' => 'Completed Invoices', 'pane' => 'completed-invoices']]])
        <div role="tabpanel" class="tab-pane active" id="pending-invoices" aria-labelledby="pending-invoices" aria-expanded="true">
            <div id="pendingInvoicesGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="completed-invoices" aria-labelledby="pending-invoices" aria-expanded="true">
            <div id="completedInvoicesGrid"></div>
        </div>
    @endcomponent
</x-app-layout>
