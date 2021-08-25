<x-app-layout>
    <x-slot name="crumb_section">Paperwork</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbCarriers = null,
                tbDrivers = null,
                tbTrucks = null,
                tbTrailers = null;
            (() => {
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    tableProperties = (type) => {
                        const typeFormatter = (params) => {
                            return  params.value ? `${params.value.replace(/^\w/, (c) => c.toUpperCase())}s` : '';
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
                                {text: 'Edit', route: '/paperwork/edit', icon: 'feather icon-edit'},
                                {route: '/paperwork/delete', type: 'delete'}
                            ],
                            container: `grid${tableName}`,
                            url: `/paperwork/search/${type.slice(0, -1)}`,
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
                <div class="row ml-0">

                    <div class="col pl-0" style="max-width: 200px">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#pane-carriers" aria-expanded="true">
                                    <i class="fas fa-dolly-flatbed mr-50 font-medium-3"></i>
                                    Carriers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#pane-drivers" aria-expanded="false">
                                    <i class="fas fa-id-card mr-50 font-medium-3"></i>
                                    Drivers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#pane-trucks" aria-expanded="false">
                                    <i class="fas fa-truck mr-50 font-medium-3"></i>
                                    Trucks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" id="account-pill-social" data-toggle="pill" href="#pane-trailers" aria-expanded="false">
                                    <i class="fas fa-trailer mr-50 font-medium-3"></i>
                                    Trailers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-carriers" aria-labelledby="pane-carriers" aria-expanded="true">
                                <div id="gridCarriers"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-drivers" aria-labelledby="pane-drivers" aria-expanded="true">
                                <div id="gridDrivers"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-trucks" aria-labelledby="pane-trucks" aria-expanded="true">
                                <div id="gridTrucks"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-trailers" aria-labelledby="pane-trailers" aria-expanded="true">
                                <div id="gridTrailers"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
