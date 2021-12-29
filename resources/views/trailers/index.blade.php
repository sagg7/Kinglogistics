<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item');
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const tableProperties = (type) => {
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        return {
                            columns: [
                                {headerName: 'Number', field: 'number'},
                                {headerName: 'Type', field: 'trailer_type', valueFormatter: nameFormatter},
                                {headerName: 'Plate', field: 'plate'},
                                {headerName: 'VIN', field: 'vin'},
                            ],
                            menu: [
                                {text: 'Edit', route: '/trailer/edit', icon: 'feather icon-edit'},
                                {route: '/trailer/delete', type: 'delete'}
                            ],
                            container: `grid${tableName}`,
                            url: `/trailer/search/${type}`,
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
                options.click((e) => {
                    const link = $(e.currentTarget).find('a'),
                        id = link.attr('href');
                    activePane(id);
                });
                activePane($('.tab-pane.active').attr('id'));
            })();
        </script>
    @endsection

    <div class="card pills-layout">
        <div class="card-content">

            <div class="card-body">
                <div class="row ml-0">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-available" aria-expanded="true">
                                    Available
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-rented" aria-expanded="false">
                                    Rented
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-oos" aria-expanded="false">
                                    Out of service
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-available" aria-labelledby="pane-available" aria-expanded="true">
                                <div class="row align-items-center">
                                    <div class="col-4 offset-8">
                                        <div class="dropdown float-right">
                                            <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                                <a href="/trailer/downloadXLS/available" class="dropdown-item"><i class="fas fa-file-excel"></i> Download report</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="gridAvailable"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-rented" aria-labelledby="pane-rented" aria-expanded="false">
                                <div class="row align-items-center">
                                    <div class="col-4 offset-8">
                                        <div class="dropdown float-right">
                                            <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                                <a href="/trailer/downloadXLS/rented" class="dropdown-item"><i class="fas fa-file-excel"></i> Download report</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="gridRented"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-oos" aria-labelledby="pane-oos" aria-expanded="false">
                                <div class="row align-items-center">
                                    <div class="col-4 offset-8">
                                        <div class="dropdown float-right">
                                            <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                                <a href="/trailer/downloadXLS/oos" class="dropdown-item"><i class="fas fa-file-excel"></i> Download report</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="gridOos"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
