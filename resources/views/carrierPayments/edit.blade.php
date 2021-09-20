<x-app-layout>
    <x-slot name="crumb_section">Payments</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js') }}"></script>
        <script>
            let _aggrid;
            const bonuses = @json($carrierPayment->bonuses);
            const expenses = @json($carrierPayment->expenses);
        </script>
        <script>
            (() => {
                let rowData = [];
                const subtotal = $('#subtotal'),
                    reductions = $('#reductions'),
                    total = $('#total');
                const numeralFormat = (value) => {
                    return numeral(value).format('$0,0.00')
                }
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                const fillTable = () => {
                        if (_aggrid) {
                            _aggrid.rowData = rowData;
                            _aggrid.gridOptions.api.setRowData(rowData);
                            _aggrid.grid.gridOptions.api.setPinnedBottomRowData(_aggrid.pinnedBottomFunction(_aggrid));
                            _aggrid.gridOptions.api.sizeColumnsToFit();
                            return;
                        }
                        _aggrid = new simpleTableAG({
                            id: 'paymentData',
                            columns: [
                                {
                                    headerName: "Date",
                                    field: "date",
                                },
                                {
                                    headerName: "Type",
                                    field: "type",
                                },
                                {
                                    headerName: "Description",
                                    field: "description",
                                },
                                {
                                    headerName: "Amount",
                                    field: "amount",
                                    valueFormatter: moneyFormatter,
                                },
                            ],
                            gridOptions: {
                                components: {
                                    tableRef: '_aggrid',
                                },
                            },
                            autoHeight: true,
                            rowData,
                        });
                        setTimeout(() => {
                            _aggrid.gridOptions.api.sizeColumnsToFit();
                        }, 300);
                    };
                bonuses.forEach(item => {
                    rowData.push({
                        id: item.id,
                        type: `Bonus`,
                        amount: item.amount,
                        date: item.date,
                        description: item.description,
                    });
                });
                expenses.forEach(item => {
                    rowData.push({
                        id: item.id,
                        type: `Expense`,
                        amount: item.amount,
                        date: item.date,
                        description: item.description,
                    });
                });
                fillTable();
                subtotal.val(numeralFormat(subtotal.val()));
                reductions.val(numeralFormat(reductions.val()));
                total.val(numeralFormat(total.val()));
            })();
        </script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div class="form-group">
                    <div id="paymentData" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
                </div>
                <div class="row">
                    <div class="col-sm-6 offset-sm-6">
                        <div class="row form-group">
                            <div class="col-6">
                                <strong>{!! Form::label('subtotal', 'Subtotal', ['class' => 'col-form-label']) !!}</strong>
                            </div>
                            <div class="col-6">
                                {!! Form::text('subtotal', $carrierPayment->gross_amount, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                            <div class="col-6">
                                <strong>{!! Form::label('reductions', 'Reductions', ['class' => 'col-form-label']) !!}</strong>
                            </div>
                            <div class="col-6">
                                {!! Form::text('reductions', $carrierPayment->reductions, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                            <div class="col-6">
                                <strong>{!! Form::label('total', 'Total', ['class' => 'col-form-label']) !!}</strong>
                            </div>
                            <div class="col-6">
                                {!! Form::text('total', $carrierPayment->total, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
        </div>
    </div>
</x-app-layout>
