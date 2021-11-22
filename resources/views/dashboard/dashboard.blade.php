<x-app-layout>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection

    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            const guard = 'web';
        </script>
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.6') }}"></script>
        <script defer>
            let tbOnCall = null;
            let tbJobs = null;
            (() => {
                const getRole = (params) => {
                    if (params.data)
                        return params.data.roles[0].name;
                };
                const percentageFormatter = (params) => {
                    if (params.value)
                        return `${params.value}%`;
                    else
                        return '';
                }
                function PhoneCallRenderer() {}
                PhoneCallRenderer.prototype.init = (params) => {
                    this.eGui = document.createElement('div');
                    if (params.value) {
                        this.eGui.innerHTML = `<a href="tel:${params.value}">${params.value}</a>`;
                    }
                }
                PhoneCallRenderer.prototype.getGui = () => {
                    return this.eGui;
                }
                function MailToRenderer() {}
                MailToRenderer.prototype.init = (params) => {
                    this.eGui = document.createElement('div');
                    if (params.value) {
                        this.eGui.innerHTML = `<a href="mailto:${params.value}">${params.value}</a>`;
                    }
                }
                MailToRenderer.prototype.getGui = () => {
                    return this.eGui;
                }
                tbOnCall = new simpleTableAG({
                    id: 'onCallTable',
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Role', field: 'role', valueFormatter: getRole},
                        {headerName: 'Email', field: 'email', cellRenderer: MailToRenderer},
                        {headerName: 'Phone', field: 'phone', cellRenderer: PhoneCallRenderer},
                    ],
                    rowData: [],
                    gridOptions: {
                        components: {
                            tableRef: 'tbOnCall',
                        },
                    },
                });
                $.ajax({
                    type: 'GET',
                    url: '/user/searchActive',
                    data: {
                        all: true,
                    },
                    success: (res) => {
                        tbOnCall.rowData = res;
                        tbOnCall.gridOptions.api.setRowData(res);
                        tbOnCall.gridOptions.api.sizeColumnsToFit();
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                });
                const tripsStatusFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1)
                            + `${params.data.status_current} of ${params.data.status_total}`;
                    else
                        return '';
                };
                const minutesAVGFormatter = (params) => {
                    if (params.value)
                        return `${params.value} min`;
                    else
                        return '';
                }
                tbJobs = new simpleTableAG({
                    id: 'jobsTable',
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Status', field: 'status', valueFormatter: tripsStatusFormatter},
                        {headerName: 'Percentage', field: 'percentage', valueFormatter: percentageFormatter},
                        {headerName: 'Miles', field: 'mileage'},
                        {headerName: 'AVG', field: 'avg', valueFormatter: minutesAVGFormatter},
                    ],
                    rowData: [],
                    gridOptions: {
                        components: {
                            tableRef: 'tbJobs',
                        },
                    },
                });
                $.ajax({
                    type: 'GET',
                    url: '/trip/dashboardData',
                    success: (res) => {
                        tbJobs.rowData = res;
                        tbJobs.gridOptions.api.setRowData(res);
                        tbJobs.gridOptions.api.sizeColumnsToFit();
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                });
            })();
            (() => {
                const barChart = (chartId, barSeries, config) => {
                    // Column Chart
                    // ----------------------------------
                    const options = {
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
                        series: barSeries,
                        legend: {
                            offsetY: -10
                        },
                        xaxis: {
                            categories: [''],
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
                    _.merge(options, config);
                    const barChart = new ApexCharts(
                        document.querySelector(`#${chartId}`),
                        options
                    );
                    barChart.render();
                };
                $.ajax({
                    url: '/driver/search',
                    type: 'GET',
                    data: {
                        graph: true,
                    },
                    success: (res) => {
                        const barSeries = [{
                            name: 'Active',
                            data: [res.active],
                        }, {
                            name: 'On Shift',
                            data: [res.onShift],
                        }, {
                            name: 'Out of Shift',
                            data: [res.outOfShift],
                        }];
                        const config = {
                            colors: [chartColorsObj.primary, chartColorsObj.success, chartColorsObj.danger],
                            yaxis: {
                                title: {
                                    text: 'Number of drivers'
                                },
                            },
                        }
                        barChart('driversChart', barSeries, config);
                    }
                });

                $.ajax({
                    url: '/trailer/search',
                    type: 'GET',
                    data: {
                        graph: true,
                    },
                    success: (res) => {
                        const barSeries = [{
                            name: 'Total',
                            data: [res.all],
                        }, {
                            name: 'In Use',
                            data: [res.available],
                        }, {
                            name: 'Available',
                            data: [res.rented],
                        }];
                        const config = {
                            yaxis: {
                                title: {
                                    text: 'Number of trailers'
                                },
                            },
                        }
                        barChart('trailersChart', barSeries, config);
                    }
                });
            })();
        </script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection

    @include('dashboard.common.loadStatus')

    <div class="row">
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>On call personnel</h3>
                </div>
                <div class="card-body">
                    <div class="card-content" style="height: 312px;">
                        <div class="aggrid ag-auto-height total-row ag-theme-material w-100" id="onCallTable" style="height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-content">
                        <div id="driversChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-content">
                        <div id="trailersChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card">
                <div class="card-header align-self-center">
                    <h3>Jobs summary</h3>
                </div>
                <div class="card-body">
                    <div class="card-content" style="height: 312px;">
                        <div class="aggrid ag-auto-height total-row ag-theme-material w-100" id="jobsTable" style="height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
