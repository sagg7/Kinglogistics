<x-app-layout>
    <x-slot name="crumb_section">Paperwork</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            let tbCarrier = null,
                tbDriver = null,
                tbTruck = null,
                tbTrailer = null,
                tbStaff = null;
            (() => {
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    tableProperties = (type) => {
                        const typeFormatter = (params) => {
                            return  params.value ? `${params.value.replace(/^\w/, (c) => c.toUpperCase())}` : '';
                        };
                        const checkFormatter = (params) => {
                            return  params.value ? 'Yes' : 'No';
                        };
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name'},
                                {headerName: 'Type', field: 'type', valueFormatter: typeFormatter},
                                {headerName: 'Required', field: 'required', valueFormatter: checkFormatter},
                            ],
                            menu: [
                                @if(auth()->user()->can(['create-paperwork']))
                                {text: 'Edit', route: '/paperwork/edit', icon: 'feather icon-edit'},
                                @endif
                                @if(auth()->user()->can(['delete-paperwork']))
                                {route: '/paperwork/delete', type: 'delete'}
                                @endif
                            ],
                            container: `grid${tableName}`,
                            url: `/paperwork/search/${type}`,
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

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row ml-0 pills-layout">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#pane-carrier" aria-expanded="true">
                                    <i class="fas fa-dolly-flatbed mr-50 font-medium-3"></i>
                                    Carriers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#pane-driver" aria-expanded="false">
                                    <i class="fas fa-id-card mr-50 font-medium-3"></i>
                                    Drivers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#pane-truck" aria-expanded="false">
                                    <i class="fas fa-truck mr-50 font-medium-3"></i>
                                    Trucks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-social" data-toggle="pill" href="#pane-trailer" aria-expanded="false">
                                    <i class="fas fa-trailer mr-50 font-medium-3"></i>
                                    Trailers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-social" data-toggle="pill" href="#pane-staff" aria-expanded="false">
                                    <i class="fas fa-users mr-50 font-medium-3"></i>
                                    Staff
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-carrier" aria-labelledby="pane-carrier" aria-expanded="true">
                                <div id="gridCarrier"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-driver" aria-labelledby="pane-driver" aria-expanded="true">
                                <div id="gridDriver"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-truck" aria-labelledby="pane-truck" aria-expanded="true">
                                <div id="gridTruck"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-trailer" aria-labelledby="pane-trailer" aria-expanded="true">
                                <div id="gridTrailer"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-staff" aria-labelledby="pane-staff" aria-expanded="true">
                                <div id="gridStaff"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
