<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Payments</x-slot>

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
                penPaymentsTable,
                dailyPaymentsTable,
                comPaymentsTable;
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
                const expensesFormatter = (params) => {
                    if (params.data.expenses) {
                        let amount = 0;
                        params.data.expenses.forEach((item) => {
                            amount += item.amount;
                        });
                        return numeral(amount).format('$0,0.00');
                    } else
                        return '$0.00';
                };
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const capitalizeNameFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };
                const paymentsColumns = [
                    {headerName: 'Date', field: 'date'},
                    {headerName: `${session['carrier'] ?? 'Carrier'}`, field: 'carrier', valueFormatter: nameFormatter},
                    {headerName: 'Subtotal', field: 'gross_amount', valueFormatter: moneyFormatter},
                    {headerName: 'Reductions', field: 'reductions', valueFormatter: moneyFormatter},
                    {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                ];
                const pendingTable = {
                    columns: [...paymentsColumns, ...[{headerName: 'Status', field: 'status', valueFormatter: capitalizeNameFormatter}]],
                    menu: [
                        @if(auth()->user()->can(['update-statement']))
                        {text: 'Edit', route: '/carrier/payment/edit', icon: 'feather icon-edit'},
                        @endif
                        {text: 'PDF', route: '/carrier/payment/downloadPDF', icon: 'fas fa-file-pdf'},
                        {text: 'XLSX', route: '/carrier/payment/downloadXLSX', icon: 'far fa-file-excel'},
                        @if(auth()->user()->can(['update-statement']))
                        {text: 'Approve', route: "/carrier/payment/approve", icon: 'fas fa-check-circle', type: 'confirm', conditional: 'status === "pending" || params.data.status === "daily"', menuData: {title: 'Set status as an approved payment?'}},
                        {text: 'Send Email & Complete', route: "/carrier/payment/complete", icon: 'fas fa-paper-plane', type: 'confirm', conditional: 'status === "approved"', menuData: {title: 'Confirm sending email to carrier?'}}
                        @endif
                    ],
                };
                penPaymentsTable = new tableAG(_.merge(pendingTable, {
                    container: 'pendingPaymentsGrid',
                    url: '/carrier/payment/search/pending',
                    tableRef: 'penPaymentsTable'
                }));
                let charges = [];
                $('.nav-pills .nav-link').click((e) => {
                    const link = $(e.currentTarget),
                        href = link.attr('href');
                    switch (href) {
                        case '#pending-payments':
                            break;
                        case '#daily-payments':
                            if (!dailyPaymentsTable)
                                dailyPaymentsTable = new tableAG(_.merge(pendingTable, {
                                    container: 'dailyPaymentsGrid',
                                    url: '/carrier/payment/search/daily',
                                    tableRef: 'dailyPaymentsTable'
                                }));
                            break;
                        case '#completed-payments':
                            if (!comPaymentsTable)
                                comPaymentsTable = new tableAG({
                                    columns: paymentsColumns,
                                    menu: [
                                        {text: 'PDF', route: '/carrier/payment/downloadPDF', icon: 'fas fa-file-pdf'},
                                        {text: 'XLSX', route: '/carrier/payment/downloadXLSX', icon: 'far fa-file-excel'},
                                        {text: 'Return To Pending', route: '/carrier/payment/pending', icon: 'fas fa-pause font-weight-bold', type: 'confirm', menuData: {title: 'Return To Pending?'}}
                                    ],
                                    container: 'completedPaymentsGrid',
                                    url: '/carrier/payment/search/completed',
                                    tableRef: 'comPaymentsTable',
                                });
                            break;
                        case '#pending-charges':
                            if (!penChargesTable)
                                penChargesTable = new tableAG({
                                    columns: [
                                        {headerName: `${session['carrier'] ?? 'Carrier'}`, field: 'name'},
                                        {headerName: 'Amount', field: 'amount', filter:false, sortable: false, valueFormatter: expensesFormatter},
                                    ],
                                    menu: [
                                        {text: 'List', route: '#view-expenses', icon: 'far fa-eye', type: 'modal'},
                                        @if(auth()->user()->can(['update-statement']))
                                        {text: 'Complete', route: '/carrier/payment/payCharges', type: 'confirm', icon: 'fas fa-check-circle', menuData: {title: 'Pay off the charges?'}},
                                        @endif
                                    ],
                                    gridOptions: {
                                        components: {
                                            OptionModalFunc: (modalId, carrierId) => {
                                                const modal = $(`${modalId}`),
                                                    content = modal.find('.content-body');
                                                content.html('<table class="table"><thead><tr>' +
                                                    '<th>Description</th><th>Amount</th>' +
                                                    '</tr></thead>' +
                                                    '<tbody></tbody></table>');
                                                const table = content.find('table'),
                                                    tbody = table.find('tbody');
                                                const carrierCharges = charges.find(obj => {
                                                    return Number(obj.id) === Number(carrierId);
                                                });
                                                carrierCharges.expenses.forEach((item) => {
                                                    tbody.append(`<tr><td>${item.description}</td><td>${numeral(item.amount).format('$0,0.00')}</td></tr>`);
                                                });
                                                content.removeClass('d-none');
                                                $('.modal-spinner').addClass('d-none');
                                                modal.modal('show');
                                            }
                                        },
                                    },
                                    container: 'pendingChargesGrid',
                                    url: '/carrier/payment/search/pendingCharges',
                                    tableRef: 'penChargesTable',
                                    successCallback: (res) => {
                                        charges = res.rows;
                                    },
                                });
                            break;
                        case '#completed-charges':
                            if (!comChargesTable)
                                comChargesTable = new tableAG({
                                    columns: [
                                        {headerName: 'Date', field: 'date'},
                                        {headerName: 'Reductions', field: 'reductions', valueFormatter: moneyFormatter},
                                        {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                                    ],
                                    menu: [
                                        {text: 'PDF', route: '/carrier/payment/downloadPDF', icon: 'fas fa-file-pdf'},
                                        {text: 'XLSX', route: '/carrier/payment/downloadXLSX', icon: 'far fa-file-excel'},
                                    ],
                                    container: 'completedChargesGrid',
                                    url: '/carrier/payment/search/completedCharges',
                                    tableRef: 'comChargesTable',
                                });
                            break;
                    }
                });
            })();
        </script>
    @endsection
    @component('components.nav-pills-form', ['pills' => [['name' => 'Pending Payments', 'pane' => 'pending-payments'],['name' => 'Daily Pay', 'pane' => 'daily-payments'],['name' => 'Completed Payments', 'pane' => 'completed-payments'],['name' => 'Pending Charges', 'pane' => 'pending-charges'],['name' => 'Completed Charges', 'pane' => 'completed-charges']]])
        <div role="tabpanel" class="tab-pane active" id="pending-payments">
            <div class="row align-items-center">
                <div class="col-4 offset-8">
                    <div class="dropdown float-right">
                        <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                            <a href="/carrier/payment/downloadXLS/pending?download=1" class="dropdown-item" id="downloadXLS"><i class="fas fa-file-excel"></i> Download Report</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div id="pendingPaymentsGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="daily-payments">
            <div id="dailyPaymentsGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="completed-payments">
            <div class="row align-items-center">
                <div class="col-4 offset-8">
                    <div class="dropdown float-right">
                        <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                            <a href="/carrier/payment/downloadXLS/completed?download=1" class="dropdown-item" id="downloadXLS"><i class="fas fa-file-excel"></i> Download Report</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div id="completedPaymentsGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="pending-charges">
            <div id="pendingChargesGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="completed-charges">
            <div id="completedChargesGrid"></div>
        </div>
    @endcomponent
</x-app-layout>
