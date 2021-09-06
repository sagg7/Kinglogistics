<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Trips</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js') }}"></script>
        <script>
            let _aggrid;
        </script>
        <script>
            (() => {
                const dateRange = $('#dateRange'),
                    trip = $('[name=trips]');
                let rowData = [],
                    barChart = null;
                const initChart = (series) => {
                        if (barChart) {
                            barChart.updateSeries(series);
                            return;
                        }
                        // Column Chart
                        // ----------------------------------
                        let options = {
                            chart: {
                                height: 350,
                                type: 'bar',
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    endingShape: 'flat',
                                    columnWidth: '55%',
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            series,
                            legend: {
                                offsetY: -10
                            },
                            xaxis: {
                                categories: [''],
                            },
                            yaxis: {
                                title: {
                                    text: 'Number of finished loads'
                                },
                            },
                            fill: {
                                opacity: 1

                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return Number(val);
                                    }
                                }
                            }
                        }
                        barChart = new ApexCharts(
                            document.querySelector("#chart"),
                            options
                        );
                        barChart.render();
                    },
                    fillTable = () => {
                        if (_aggrid) {
                            _aggrid.rowData = rowData;
                            _aggrid.gridOptions.api.setRowData(rowData);
                            _aggrid.grid.gridOptions.api.setPinnedBottomRowData(_aggrid.pinnedBottomFunction(_aggrid));
                            _aggrid.gridOptions.api.sizeColumnsToFit();
                            return;
                        }
                        _aggrid = new simpleTableAG({
                            id: 'reportTable',
                            columns: [
                                {
                                    headerName: "Date",
                                    field: "date",
                                },
                                {
                                    headerName: "Driver",
                                    field: "driver",
                                },
                                {
                                    headerName: "Control #",
                                    field: "control",
                                },
                                {
                                    headerName: "Origin",
                                    field: "origin",
                                },
                                {
                                    headerName: "Destination",
                                    field: "destination",
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
                    },
                    getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
                        $.ajax({
                            url: '/report/loadsData',
                            type: 'GET',
                            data: {
                                start: start.format('YYYY/MM/DD'),
                                end: end.format('YYYY/MM/DD'),
                                trip: trip.val(),
                            },
                            success: (res) => {
                                rowData = [];
                                let series = [];
                                res.forEach((item, i) => {
                                    series.push({
                                        name: item.name,
                                        data: [item.loads_count],
                                    });
                                    item.loads.forEach(load => {
                                        rowData.push({
                                            id: load.id,
                                            date: load.date,
                                            driver: item.name,
                                            control: load.control_number,
                                            origin: load.origin,
                                            destination: load.destination,
                                        });
                                    });
                                });
                                initChart(series);
                                fillTable();
                            }
                        })
                    };
                dateRange.daterangepicker({
                    format: 'YYYY/MM/DD',
                    locale: dateRangeLocale,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                }, (start, end, label) => {
                    getData(start, end);
                });
                trip.select2({
                    placeholder: 'Select',
                    allowClear: true,
                }).on('select2:select', () => {
                    getData();
                }).on('select2:unselect', () => {
                    getData();
                });
                getData();
            })();
        </script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <fieldset class="form-group">
                        <label for="dateRange">Select Dates</label>
                        <input type="text" id="dateRange" class="form-control">
                    </fieldset>
                </div>
                <div class="col-6">
                    <fieldset class="form-group">
                        <label for="trips">Trip</label>
                        {!! Form::select('trips', $trips, null, ['class' => 'form-control']) !!}
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div id="chart"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div id="reportTable" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
        </div>
    </div>
</x-app-layout>
