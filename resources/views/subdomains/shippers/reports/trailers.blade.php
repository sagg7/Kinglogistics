<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Trailers</x-slot>

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
                let rowData = [],
                    barChart = null;
                const initChart = (data) => {
                        const barSeries = [{
                            name: 'Trailers',
                            data: [data.trailers],
                        }, {
                            name: 'Boxes',
                            data: [data.boxes],
                        }];
                        if (barChart) {
                            barChart.updateSeries(barSeries);
                            return;
                        }
                        // Column Chart
                        // ----------------------------------
                        let options = {
                            chart: {
                                height: 350,
                                type: 'bar',
                            },
                            colors: [chartColorsObj.success, chartColorsObj.primary],
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
                            series: barSeries,
                            legend: {
                                offsetY: -10
                            },
                            xaxis: {
                                categories: [''],
                            },
                            yaxis: {
                                title: {
                                    text: 'Equipment count'
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
                                    headerName: "Trailer",
                                    field: "number",
                                },
                                {
                                    headerName: "Chassis",
                                    field: "chassis",
                                },
                                {
                                    headerName: "Truck",
                                    field: "truck",
                                },
                                {
                                    headerName: "Box Type",
                                    field: "box",
                                },
                                {
                                    headerName: "Box Number",
                                    field: "boxNumber",
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
                    getData = () => {
                        $.ajax({
                            url: '/report/trailersData',
                            type: 'GET',
                            success: (res) => {
                                let trailerCount = 0,
                                    boxCount = 0;
                                res.forEach(item => {
                                    trailerCount++;
                                    const trailer = item;
                                    const chassis = item.chassis_type ? item.chassis_type : {};
                                    const truck = trailer.truck ? trailer.truck : {};
                                    const driver = truck.driver ? truck.driver : {};
                                    const load = driver.latest_load ? driver.latest_load : {};
                                    const boxEnd = load.box_end ? load.box_end : {};
                                    if (load.box_status_end === "loaded")
                                        boxCount++;
                                    rowData.push({
                                        id: item.id,
                                        number: trailer.number,
                                        chassis: chassis.name,
                                        truck: item.truck.number,
                                        box: boxEnd.name,
                                        boxNumber: load.box_number_end,
                                    });
                                    initChart({trailers: trailerCount, boxes: boxCount});
                                });
                                fillTable();
                            }
                        })
                    };
                getData();
            })();
        </script>
    @endsection

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="reportTable" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
