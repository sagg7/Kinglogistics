<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbUninspected = null,
                tbDelivered = null,
                tbFinished = null;
            const dateRange = $('#dateRange');
            (() => {
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const numberFormatter = (params) => {
                    if (params.value)
                        return params.value.number;
                    else
                        return '';
                };
                const periodFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '';
                };
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    tableProperties = (type) => {
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        return {
                            columns: [
                                //{headerName: 'Fecha', field: 'date'},
                                {headerName: 'Date', field: 'date'},
                                {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                                {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                                {headerName: 'Trailer', field: 'trailer', valueFormatter: numberFormatter},
                                {headerName: 'Period', field: 'period', valueFormatter: periodFormatter},
                                {headerName: 'Cost', field: 'cost', valueFormatter: moneyFormatter},
                                {headerName: 'Deposit', field: 'deposit', valueFormatter: moneyFormatter},
                                // TODO: ADD FINISH DATE AND DELIVERED DATE
                            ],
                            menu: [
                                {text: 'Check out', route: "/inspection/create", icon: 'feather icon-edit', type: 'dynamic', conditional:'status == "uninspected"'},
                                {text: 'Check in', route: "/inspection/endInspection", icon: 'feather icon-edit', type: 'dynamic', conditional:'status == "delivered"'},
                                {text: 'Edit', route: '/rental/edit', icon: 'feather icon-edit'},
                                {route: '/rental/delete', type: 'delete'}
                            ],
                            container: `grid${tableName}`,
                            url: `/rental/search/${type}`,
                            tableRef: `tb${tableName}`,
                        };
                    },
                    initTables = (type) => {
                        let table = `tb${type.replace(/^\w/, (c) => c.toUpperCase())}`;
                        if (!window[table])
                            window[table] = new tableAG(tableProperties(`${type}`));
                    },
                    activePane = (id) => {
                        const type = id.split('-')[1];
                        initTables(type);
                    };
                const dateFilter = $('#dateFilter');
                options.click((e) => {
                    const link = $(e.currentTarget).find('a'),
                        id = link.attr('href');
                    if (id.split('-')[1] === "finished")
                        dateFilter.removeClass('d-none');
                    else
                        dateFilter.addClass('d-none');
                    activePane(id);
                });
                activePane($('.tab-pane.active').attr('id'));
                $('#downloadXLS').click((e) => {
                    e.preventDefault();
                    const type = $('.tab-pane.active').attr('id').split('-')[1];
                    window.location.href = `/rental/downloadXLS/${type}?start=${dateRange.data('daterangepicker').startDate.format('YYYY-MM-DD')}&end=${dateRange.data('daterangepicker').endDate.format('YYYY-MM-DD')}`;
                });
                dateRange.daterangepicker({
                    format: 'YYYY/MM/DD',
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                }, (start, end, label) => {
                    tbFinished.searchQueryParams = _.merge(
                        tbFinished.searchQueryParams,
                        {
                            start: start.format('YYYY/MM/DD'),
                            end: end.format('YYYY/MM/DD'),
                        });
                    tbFinished.updateSearchQuery();
                });
            })();
        </script>
    @endsection

    <div class="card pills-layout">
        <div class="card-content">

            <div class="card-header">
                <div class="col-4">
                    <div class="d-none" id="dateFilter">
                        <label for="dateRange">Select Dates</label>
                        <input type="text" id="dateRange" class="form-control">
                    </div>
                </div>
                <div class="col-4 offset-4">
                    <div class="dropdown float-right">
                        <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                            <a href="#" class="dropdown-item" id="downloadXLS"><i class="fas fa-file-excel"></i> Download Report</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="card-body">
                <div class="row ml-0">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-uninspected" aria-expanded="true">
                                    Uninspected
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-delivered" aria-expanded="false">
                                    Delivered
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-finished" aria-expanded="false">
                                    Finished
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-uninspected" aria-labelledby="pane-uninspected" aria-expanded="true">
                                <div id="gridUninspected"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-delivered" aria-labelledby="pane-delivered" aria-expanded="false">
                                <div id="gridDelivered"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-finished" aria-labelledby="pane-finished" aria-expanded="false">
                                <div id="gridFinished"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
