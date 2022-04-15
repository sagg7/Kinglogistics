<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Search</x-slot>

    @section('head')
    <style>
        .ag-theme-material .ag-header-cell, .ag-theme-material .ag-header-group-cell,
        .ag-theme-material .ag-cell {
            padding-left: 5px;
            padding-right: 5px;
        }
        /*span.ag-header-icon.ag-header-cell-menu-button {
            display: none;
        }*/
        #dataTable, .aggrid .ag-header-cell-text {
            font-size: .9rem!important;
        }
    </style>
    @endsection

    @section('modals')
        @include("subdomains.carriers.loads.road.modals.loadDetails")
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script defer>
            (() => {
                const origin_city = $('#origin_city');
                const origin_radius = $('#origin_radius');
                const destination_city = $('#destination_city');
                const destination_radius = $('#destination_radius');
                const trailer_type = $('#trailer_type');
                const load_size = $('#load_size');
                const ship_date = $('#ship_date');
                const weight = $('#weight');
                const length = $('#length');
                const modal = $('#loadDetails');

                const currencyFormatter = (value) => {
                    // If not a number, return value unchanged
                    if (isNaN(value))
                        return value
                    else // else returned the formatted value
                        return numeral(value).format('$0,0.00');
                };
                const capitalizeFormatter = (value) => {
                    if (value)
                        return value.charAt(0).toUpperCase()  + value.slice(1);
                    else
                        return '';
                };
                const dataTable = new simpleTableAG({
                    id: 'dataTable',
                    columns: [
                        {headerTooltip: 'Age', headerName: 'Age', field: 'age'},
                        {headerTooltip: 'Deadhead Miles', headerName: 'D/H Miles', field: 'deadhead_miles'},
                        {headerTooltip: 'Trip Miles', headerName: 'Trip Miles', field: 'mileage'},
                        {headerTooltip: 'Origin', headerName: 'Origin', field: 'origin_city'},
                        {headerTooltip: 'State', headerName: 'ST', field: 'origin_state'},
                        {headerTooltip: 'Destination', headerName: 'Destination', field: 'destination_city'},
                        {headerTooltip: 'State', headerName: 'ST', field: 'destination_state'},
                        {headerTooltip: 'Trailer Type', headerName: 'Trailer Type', field: 'trailer_type'},
                        {headerTooltip: 'Load Size', headerName: 'Load Size', field: 'load_size'},
                        {headerTooltip: 'Length', headerName: 'Length', field: 'length'},
                        {headerTooltip: 'Weight', headerName: 'Weight', field: 'weight'},
                        {headerTooltip: 'Payrate', headerName: 'Payrate', field: 'pay_rate'},
                        {headerTooltip: 'Estimated Rate per Mile', headerName: 'Est. Rate per Mile', field: 'rate_mile'},
                        {headerTooltip: 'Ship Date', headerName: 'Ship Date', field: 'date'},
                        {headerTooltip: 'Quick Pay', headerName: 'Quick Pay', field: 'quick_pay'},
                        {headerTooltip: 'Company', headerName: 'Company', field: 'shipper'},
                    ],
                    rowData: [],
                    gridOptions: {
                        components: {
                            tableRef: `dataTable`,
                        },
                        tooltipShowDelay: 500,
                        onRowClicked: (event) => {
                            const data = event.data;
                            console.log(data);
                            if (data.origin_city)
                                $('#route_string').text(`${data.origin_city}, ${data.origin_state} » ${data.destination_city}, ${data.destination_state}`);
                            $('#load_details_age').text(data.age);
                            $('#load_details_mileage').text(data.mileage);
                            $('#load_details_trailer').text(data.trailer_type);
                            $('#load_details_size').text(data.load_size);
                            $('#load_details_length').text(data.length);
                            $('#load_details_weight').text(data.weight);
                            $('#load_details_width').text(data.width);
                            $('#load_details_height').text(data.height);
                            $('#load_details_payrate').text(data.pay_rate);
                            $('#load_details_date').text(data.date);
                            //$('#load_details_delivery').text(data.);
                            //$('#load_details_comments').text(data.);
                            $('#shipper_name').text(data.shipper);
                            $('#shipper_phone').text(data.shipper_phone);
                            modal.find('.modal-spinner').addClass('d-none');
                            modal.find('.content-body').removeClass('d-none');
                            //const selectedRows = dataTable.gridOptions.api.getSelectedRows();
                            modal.modal('show');
                            dataTable.gridOptions.api.deselectAll();
                        },
                    },
                    autoHeight: true,
                });

                const getData = () => {
                    $.ajax({
                        url: '/load/road/search',
                        type: 'GET',
                        data: {
                            origin_city: origin_city.val(),
                            origin_radius: origin_radius.val(),
                            destination_city: destination_city.val(),
                            destination_radius: destination_radius.val(),
                            trailer_type: trailer_type.val(),
                            load_size: load_size.val(),
                            ship_date_start: ship_date.data().daterangepicker.startDate.format('YYYY/MM/DD'),
                            ship_date_end: ship_date.data().daterangepicker.endDate.format('YYYY/MM/DD'),
                            weight: weight.val(),
                            length: length.val(),
                        },
                        success: (res) => {
                            let rowData = [];
                            res.forEach(item => {
                                rowData.push({
                                    age: item.age,
                                    deadhead_miles: item.road.deadhead_miles, // TODO: Calculate deadhead
                                    mileage: item.mileage,
                                    origin_city: item.road.origin_city ? item.road.origin_city.name : null,
                                    origin_state: item.road.origin_city ? item.road.origin_city.state.abbreviation : null,
                                    destination_city: item.road.destination_city ? item.road.destination_city.name : null,
                                    destination_state: item.road.destination_city ? item.road.destination_city.state.abbreviation : null,
                                    trailer_type: item.road.trailer_type.name,
                                    load_size: capitalizeFormatter(item.road.load_size),
                                    length: item.road.length,
                                    weight: item.weight,
                                    pay_rate: item.road.pay_rate,
                                    rate_mile: currencyFormatter(item.rate_mile),
                                    date: item.date,
                                    shipper: item.shipper.name,
                                    // TODO: ADD THIS DATA
                                    quick_pay: null,
                                    shipper_phone: item.shipper ? item.shipper.phone : null,
                                });
                            });
                            dataTable.rowData = rowData;
                            dataTable.gridOptions.api.setRowData(rowData);
                            //dataTable.gridOptions.api.sizeColumnsToFit();
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    });
                };

                origin_city.select2({
                }).on('select2:select', () => {
                    getData();
                }).on('select2:unselect', () => {
                    getData();
                });
                destination_city.select2({
                }).on('select2:select', () => {
                    getData();
                }).on('select2:unselect', () => {
                    getData();
                });
                trailer_type.select2({
                }).on('select2:select', () => {
                    getData();
                }).on('select2:unselect', () => {
                    getData();
                });
                // Normal selects
                origin_radius.change(() => {
                    getData();
                })
                destination_radius.change(() => {
                    getData();
                })
                load_size.change(() => {
                    getData();
                })
                weight.change(() => {
                    getData();
                })
                length.change(() => {
                    getData();
                })

                ship_date.daterangepicker({
                    format: 'YYYY/MM/DD',
                }, (start, end, label) => {
                    getData();
                });
            })();
        </script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="row form-group">
                <div class="col-8">
                    {!! Form::label('origin_city', 'Origin city, State(s) or Zipcode') !!}
                    {!! Form::select('origin_city', [], null, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <div class="col-4">
                    {!! Form::label('origin_radius', 'Radius') !!}
                    {!! Form::select('origin_radius', $radius, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col-8">
                    {!! Form::label('destination_city', 'Destination city, State(s) or Zipcode') !!}
                    {!! Form::select('destination_city', [], null, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <div class="col-4">
                    {!! Form::label('destination_radius', 'Radius') !!}
                    {!! Form::select('destination_radius', $radius, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    {!! Form::label('trailer_type', 'Trailer type') !!}
                    {!! Form::select('trailer_type', $trailer_types, null, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <div class="col">
                    {!! Form::label('load_size', 'Load size') !!}
                    {!! Form::select('load_size', $load_sizes, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col">
                    {!! Form::label('ship_date', 'Ship date') !!}
                    {!! Form::text('ship_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col">
                    {!! Form::label('weight', 'Weight') !!}
                    {!! Form::select('weight', $weight, null, ['class' => 'form-control']) !!}
                </div>
                <div class="col">
                    {!! Form::label('length', 'Length') !!}
                    {!! Form::select('length', $length, null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="dataTable" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
        </div>
    </div>
</x-app-layout>
