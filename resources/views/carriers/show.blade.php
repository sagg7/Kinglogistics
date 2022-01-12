<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Summary</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            const carrier_id = Number({{ $carrier->id }});
            let _gridTrailers;
            let _gridDrivers;
            (() => {
                const currencyFormatter = (params) => {
                    return numeral(params.value).format('$0,0.00');
                }
                const capitalizeFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };
                let trailersData = [];
                let driversData = [];
                _gridTrailers = new simpleTableAG({
                    id: 'trailersTable',
                    columns: [
                        {
                            headerName: "Name",
                            field: "name",
                        },
                        {
                            headerName: "Status",
                            field: "status",
                            valueFormatter: capitalizeFormatter,
                        },
                        {
                            headerName: "Cost",
                            field: "cost",
                            valueFormatter: currencyFormatter,
                        },
                    ],
                    gridOptions: {
                        components: {
                            tableRef: 'window[gridName]',
                        },
                    },
                    rowData: trailersData,
                });
                _gridDrivers = new simpleTableAG({
                    id: 'driversTable',
                    columns: [
                        {
                            headerName: "Name",
                            field: "name",
                        },
                        {
                            headerName: "Loads",
                            field: "loads",
                        },
                    ],
                    gridOptions: {
                        components: {
                            tableRef: 'window[gridName]',
                        },
                    },
                    rowData: driversData,
                });

                const yearIncome = $('#yearIncome');
                const monthIncome = $('#monthIncome');
                const lastWeekIncome = $('#lastWeekIncome');
                const weekIncome = $('#weekIncome');
                const lastWeekLoads = $('#lastWeekLoads');
                const weekLoads = $('#weekLoads');
                const incidents = $('#incidents');
                const carrierStatus = $('#status');
                const formatCurrency = () => {
                    $.each($('.currency'), (i, el) => {
                        const element = $(el);
                        element.text(numeral(element.text()).format('$0,0.00'));
                    });
                }
                const fillTableDrivers = () => {
                    _gridDrivers.rowData = driversData;
                    _gridDrivers.gridOptions.api.setRowData(driversData);
                    _gridDrivers.grid.gridOptions.api.setPinnedBottomRowData(_gridDrivers.pinnedBottomFunction(_gridDrivers));
                    _gridDrivers.gridOptions.api.sizeColumnsToFit();
                }
                const fillTableTrailers = () => {
                    _gridTrailers.rowData = trailersData;
                    _gridTrailers.gridOptions.api.setRowData(trailersData);
                    _gridTrailers.grid.gridOptions.api.setPinnedBottomRowData(_gridTrailers.pinnedBottomFunction(_gridTrailers));
                    _gridTrailers.gridOptions.api.sizeColumnsToFit();
                }
                $.ajax({
                    url: `/carrier/summaryData/${carrier_id}`,
                    success: (res) => {
                        // Set Currency Values
                        yearIncome.text(res.incomeYear);
                        monthIncome.text(res.incomePastMonth);
                        lastWeekIncome.text(res.incomePastWeek);
                        weekIncome.text(res.incomeWeek);
                        formatCurrency();
                        // Set Simple Data
                        carrierStatus.text(res.status);
                        lastWeekLoads.text(res.totalLoadsPastWeek);
                        weekLoads.text(res.totalLoadsWeek);
                        incidents.text(res.incidents);
                        // Set Drivers Table
                        res.drivers.forEach(item => {
                            driversData.push({
                                name: item.name,
                                loads: item.loads_count,
                            });
                        });
                        fillTableDrivers();
                        // Set Rentals Table
                        res.rentals.forEach(item => {
                            trailersData.push({
                                name: item.trailer.number,
                                status: item.trailer.status,
                                cost: item.cost,
                            });
                        });
                        fillTableTrailers()
                    }
                });
            })();
        </script>
    @endsection

    <div class="card border border-2 border-primary">
        <div class="card-content">
            <div class="card-header align-items-center">
                <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                    <div class="avatar-content">
                        <i class="fas fa-dolly-flatbed font-size-large text-primary"></i>
                    </div>
                </div>
                <div class="col">
                    <h1>{{ $carrier->name }}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card text-center border border-2 border-success">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-dollar-sign font-size-large text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>$ THIS YEAR</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 class="currency" id="yearIncome"><div class="spinner-grow text-success"></div></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-success">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-dollar-sign font-size-large text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>$ LAST MONTH</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 class="currency" id="monthIncome"><div class="spinner-grow text-success"></div></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-success">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-dollar-sign font-size-large text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>$ LAST WEEK</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 class="currency" id="lastWeekIncome"><div class="spinner-grow text-success"></div></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-success">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-dollar-sign font-size-large text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>$ WEEK IN COURSE</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 class="currency" id="weekIncome"><div class="spinner-grow text-success"></div></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-warning">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-trophy font-size-large text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>RANKING</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card text-center border border-2 border-danger">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-bell font-size-large text-danger"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>ALERTS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-info">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-clipboard-check font-size-large text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>STATUS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 id="status"><div class="spinner-grow text-info"></div></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-info">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-truck-loading font-size-large text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>LAST WEEK LOADS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 id="lastWeekLoads"><div class="spinner-grow text-info"></div></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-info">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-truck-loading font-size-large text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>THIS WEEK LOADS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 id="weekLoads"><div class="spinner-grow text-info"></div></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card border border-2 border-secondary">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-secondary p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-trailer font-size-large text-secondary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>CHASSIS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div style="height: 360px;">
                            <div id="trailersTable" class="aggrid ag-auto-height total-row ag-theme-material w-100" style="height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border border-2 border-secondary">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-secondary p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-id-card font-size-large text-secondary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>DRIVERS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div style="height: 360px;">
                            <div id="driversTable" class="aggrid ag-auto-height total-row ag-theme-material w-100" style="height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center border border-2 border-danger">
                <div class="card-header align-self-center text-center">
                    <div class="col-12">
                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-car-crash font-size-large text-danger"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4><strong>INCIDENTS</strong></h4>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <h1 id="incidents"><div class="spinner-grow text-danger"></div></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
