<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                const percentageFormatter = (params) => {
                    if (params.value)
                        return Number(params.value) + '%';
                    else
                        return '0%';
                };
                const statusFormatter = (params) => {
                    if (params.value)
                        return 'Paid';
                    else
                        return 'Active';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                        {headerName: 'Paid', field: 'paid_amount', valueFormatter: moneyFormatter},
                        {headerName: 'Installments', field: 'installments'},
                        {headerName: 'Paid Installments', field: 'paid_installments'},
                        {headerName: 'Fee', field: 'fee_percentage', valueFormatter: percentageFormatter},
                        {headerName: 'Status', field: 'is_paid', filter:false, valueFormatter: statusFormatter},
                    ],
                    container: 'myGrid',
                    url: '/loan/search',
                    tableRef: 'tbAG',
                });
                const dateRange = $('#dateRange');
                dateRange.daterangepicker({
                    format: 'YYYY/MM/DD',
                    locale: dateRangeLocale,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                }, (start, end, label) => {
                    tbAG.searchQueryParams = {
                        start: start.format('YYYY/MM/DD'),
                        end: end.format('YYYY/MM/DD'),
                    };
                    tbAG.updateSearchQuery();
                });
            })();
        </script>
    @endsection

    <div class="card">
        <div class="card-header">
            <fieldset class="form-group col-12">
                <label for="dateRange">Select Dates</label>
                <input type="text" id="dateRange" class="form-control">
            </fieldset>
        </div>
    </div>

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
