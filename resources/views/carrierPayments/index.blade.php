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
                    {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                    {headerName: 'Subtotal', field: 'gross_amount', valueFormatter: moneyFormatter},
                    {headerName: 'Reductions', field: 'reductions', valueFormatter: moneyFormatter},
                    {headerName: 'Total', field: 'total', valueFormatter: moneyFormatter},
                ];
                penPaymentsTable = new tableAG({
                    columns: [...paymentsColumns, ...[{headerName: 'Status', field: 'status', valueFormatter: capitalizeNameFormatter}]],
                    menu: [
                        {text: 'PDF', route: '/carrier/payment/downloadPDF', icon: 'fas fa-file-pdf'},
                        {text: 'Approve', route: "/carrier/payment/approve", icon: 'fas fa-check-circle', type: 'confirm', conditional: 'status === "pending"', menuData: {title: 'Set status as an approved payment?'}},
                        {text: 'Send Email & Complete', route: "/carrier/payment/complete", icon: 'fas fa-paper-plane', type: 'confirm', conditional: 'status === "approved"', menuData: {title: 'Confirm sending email to carrier?'}}
                    ],
                    container: 'pendingPaymentsGrid',
                    url: '/carrier/payment/search/pending',
                    tableRef: 'penPaymentsTable',
                });
                let charges = [];
                $('.nav-pills .nav-link').click((e) => {
                    const link = $(e.currentTarget),
                        href = link.attr('href');
                    switch (href) {
                        case '#pending-payments':
                            break;
                        case '#completed-payments':
                            if (!comPaymentsTable)
                                comPaymentsTable = new tableAG({
                                    columns: paymentsColumns,
                                    menu: [
                                        {text: 'PDF', route: '/carrier/payment/downloadPDF', icon: 'fas fa-file-pdf'},
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
                                        {headerName: 'Carrier', field: 'name'},
                                        {headerName: 'Amount', field: 'amount', filter:false, valueFormatter: expensesFormatter},
                                    ],
                                    menu: [
                                        {text: 'List', route: '#view-expenses', icon: 'far fa-eye', modal: true},
                                        {text: 'Complete', route: '/carrier/payment/payCharges', type: 'confirm', icon: 'fas fa-check-circle', menuData: {title: 'Pay off the charges?'}},
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
    @component('components.nav-pills-form', ['pills' => [['name' => 'Pending Payments', 'pane' => 'pending-payments'],['name' => 'Completed Payments', 'pane' => 'completed-payments'],['name' => 'Pending Charges', 'pane' => 'pending-charges'],['name' => 'Completed Charges', 'pane' => 'completed-charges']]])
        <div role="tabpanel" class="tab-pane active" id="pending-payments" aria-labelledby="pending-payments" aria-expanded="true">
            <div id="pendingPaymentsGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="completed-payments" aria-labelledby="pending-charges" aria-expanded="true">
            <div id="completedPaymentsGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="pending-charges" aria-labelledby="completed-payments" aria-expanded="true">
            <div id="pendingChargesGrid"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="completed-charges" aria-labelledby="completed-charges" aria-expanded="true">
            <div id="completedChargesGrid"></div>
        </div>
    @endcomponent
</x-app-layout>
