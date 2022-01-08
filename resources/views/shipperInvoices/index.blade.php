<x-app-layout>
    <x-slot name="crumb_section">Customer</x-slot>
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
            var penInvoicesTable,
                comInvoicesTable,
                paidInvoicesTable;
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
                const loadFormatter = (params) => {
                    if (params.value && params.value[0])
                        return params.value[0].trip.name;
                    else
                        return '';
                };
                const invoicesColumns = [
                    {headerName: 'Date', field: 'date'},
                    {headerName: 'Invoice#', field: 'custom_id'},
                    {headerName: 'Job', field: 'loads', valueFormatter: loadFormatter},
                    {headerName: 'Customer', field: 'shipper', valueFormatter: nameFormatter},
                    {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                ];
                penInvoicesTable = new tableAG({
                    columns: invoicesColumns,
                    menu: [
                        {text: 'PDF', route: '/shipper/invoice/downloadPDF', icon: 'fas fa-file-pdf'},
                        {text: 'XLSX', route: '/shipper/invoice/downloadXLSX', icon: 'far fa-file-excel'},
                        {text: 'Download Pictures', route: '/shipper/invoice/downloadPhotos', icon: 'far fa-file-image-o'},
                        @if(auth()->user()->can(['update-invoice']))
                        {text: 'Send Email & Complete', route: '/shipper/invoice/complete', type: 'confirm', icon: 'fas fa-paper-plane',
                            menuData: {
                                title: 'Confirm sending email to customer?',
                                afterConfirmFunction: () => {
                                    if (comInvoicesTable)
                                        comInvoicesTable.updateSearchQuery();
                                },
                            }
                        },
                        @endif
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
                                        {text: 'XLSX', route: '/shipper/invoice/downloadXLSX', icon: 'far fa-file-excel'},
                                        @if(auth()->user()->can(['update-invoice']))
                                        {text: 'Set as paid', route: '/shipper/invoice/pay', type: 'confirm', icon: 'fas fa-hand-holding-usd font-weight-bold',
                                            menuData: {
                                                title: 'Set status to paid?',
                                                afterConfirmFunction: () => {
                                                    if (paidInvoicesTable)
                                                        paidInvoicesTable.updateSearchQuery();
                                                },
                                            }
                                        },
                                        {text: 'Return to pending', route: '/shipper/invoice/pending', type: 'confirm', icon: 'fas fa-undo-alt font-weight-bold',
                                            menuData: {
                                                title: 'Return status to pending?',
                                                afterConfirmFunction: () => {
                                                    if (penInvoicesTable)
                                                        penInvoicesTable.updateSearchQuery();
                                                },
                                            }
                                        },
                                        @endif
                                        {text: 'Download Pictures', route: '/shipper/invoice/downloadPhotos', icon: 'far fa-file-image-o'},
                                    ],
                                    container: 'completedInvoicesGrid',
                                    url: '/shipper/invoice/search/completed',
                                    tableRef: 'comInvoicesTable',
                                });
                            break;
                        case '#paid-invoices':
                            if (!paidInvoicesTable)
                                paidInvoicesTable = new tableAG({
                                    columns: invoicesColumns,
                                    menu: [
                                        {text: 'PDF', route: '/shipper/invoice/downloadPDF', icon: 'fas fa-file-pdf'},
                                        {text: 'XLSX', route: '/shipper/invoice/downloadXLSX', icon: 'far fa-file-excel'},
                                        {text: 'Download Pictures', route: '/shipper/invoice/downloadPhotos', icon: 'far fa-file-image-o'},
                                    ],
                                    container: 'paidInvoicesGrid',
                                    url: '/shipper/invoice/search/paid',
                                    tableRef: 'paidInvoicesTable',
                                });
                            break;
                    }
                });

                $('#completeAll').click((e) => {
                    e.preventDefault();
                    if (penInvoicesTable.dataSource.data.rows.length > 0)
                        confirmMsg({
                            config: {title: 'Send emails for all pending invoices and set status as complete?'},
                            onConfirm: () => {
                                $.ajax({
                                    url: '/shipper/invoice/completeAll',
                                    type: 'POST',
                                    success: () => {
                                        penInvoicesTable.updateSearchQuery();
                                        if (comInvoicesTable)
                                            comInvoicesTable.updateSearchQuery();
                                    },
                                    error: () => {
                                        throwErrorMsg();
                                    }
                                });
                            }
                        });
                    else
                        throwErrorMsg('There are no pending invoices');
                });

                $('#payAll').click((e) => {
                    e.preventDefault();
                    if (comInvoicesTable.dataSource.data.rows.length > 0)
                        confirmMsg({
                            config: {title: 'Set all completed invoices as paid?'},
                            onConfirm: () => {
                                $.ajax({
                                    url: '/shipper/invoice/payAll',
                                    type: 'POST',
                                    success: () => {
                                        comInvoicesTable.updateSearchQuery();
                                        if (paidInvoicesTable)
                                            paidInvoicesTable.updateSearchQuery();
                                    },
                                    error: () => {
                                        throwErrorMsg();
                                    }
                                });
                            }
                        });
                    else
                        throwErrorMsg('There are no completed invoices');
                });
            })();
        </script>
    @endsection
    @component('components.nav-pills-form', ['pills' => [
    ['name' => 'Pending Invoices', 'pane' => 'pending-invoices'],
    ['name' => 'Completed Invoices', 'pane' => 'completed-invoices'],
    ['name' => 'Paid Invoices', 'pane' => 'paid-invoices'],
    ]])
        <div role="tabpanel" class="tab-pane active" id="pending-invoices" aria-labelledby="pending-invoices" aria-expanded="true">
            <div class="row">
                <div class="col-6 offset-6">
                    <div class="dropdown float-right">
                        <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                            <a class="dropdown-item" id="completeAll"><i class="fas fa-paper-plane"></i> Send Emails & Complete</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="pendingInvoicesGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="completed-invoices" aria-labelledby="pending-invoices" aria-expanded="true">
            <div class="row">
                <div class="col-6 offset-6">
                    <div class="dropdown float-right">
                        <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                            <a class="dropdown-item" id="payAll"><i class="fas fa-hand-holding-usd"></i> Set all invoices as paid</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="completedInvoicesGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="paid-invoices" aria-labelledby="paid-invoices" aria-expanded="true">
            <div id="paidInvoicesGrid"></div>
        </div>
    @endcomponent
</x-app-layout>
