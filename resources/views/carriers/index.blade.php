<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAll = null,
                tbDeleted = null,
                tbNotRehirable = null;
            (() => {
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    tableProperties = (type) => {
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        let menu;
                        switch (type) {
                            default:
                                menu = [
                                    {text: 'Edit', route: '/carrier/edit', icon: 'feather icon-edit'},
                                    {
                                        text: 'Prospect',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "prospect"},
                                        icon: 'fas fa-check-circle',
                                        type: 'confirm',
                                        conditional: 'status === "interested"',
                                        menuData: {title: 'Set status as prospect?'}
                                    },
                                    {
                                        text: 'Ready to work',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "ready"},
                                        icon: 'fas fa-check-circle',
                                        type: 'confirm',
                                        conditional: 'status === "prospect"',
                                        menuData: {title: 'Set status as ready to work?'}
                                    },
                                    {
                                        text: 'Active',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "active"},
                                        icon: 'fas fa-check-circle',
                                        type: 'confirm',
                                        conditional: 'status === "ready_to_work" || params.data.status === "not_working"',
                                        menuData: {title: 'Set status as active?'}
                                    },
                                    {
                                        text: 'Not working',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "not_working"},
                                        icon: 'far fa-times-circle',
                                        type: 'confirm',
                                        conditional: 'status === "active"',
                                        menuData: {title: 'Set status as not working?'}
                                    },
                                    {
                                        text: 'Not rehirable',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "not_rehirable"},
                                        icon: 'fas fa-ban font-weight-bold',
                                        type: 'confirm',
                                        conditional: 'status === "active"',
                                        menuData: {title: 'Set status as not rehirable?'}
                                    },
                                    {route: '/carrier/delete', type: 'delete'},
                                ];
                                break;
                            case "deleted":
                                menu = [
                                    {text: 'Restore', route: '/carrier/restore', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Restore carrier?'}}
                                ];
                                break;
                            case "notRehirable":
                                menu = [
                                    @if(auth()->user()->hasRole('admin'))
                                    {text: 'Restore', route: '/carrier/restore', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Restore carrier?'}}
                                    @endif
                                ];
                                break;
                        }
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name'},
                                {headerName: 'Email', field: 'email'},
                                {headerName: 'Phone', field: 'phone'},
                            ],
                            menu,
                            container: `grid${tableName}`,
                            url: `/carrier/search/${type}`,
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

    <!-- TODO: ADD DOCUMENT PROGRESS MODAL -->

    <div class="card pills-layout">
        <div class="card-content">

            <div class="card-body">
                <div class="row ml-0">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-all" aria-expanded="true">
                                    All carriers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-deleted" aria-expanded="false">
                                    Deleted
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-notRehirable" aria-expanded="false">
                                    Not rehirable
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-all" aria-labelledby="pane-all" aria-expanded="true">
                                <div id="gridAll"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-deleted" aria-labelledby="pane-deleted" aria-expanded="true">
                                <div id="gridDeleted"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-notRehirable" aria-labelledby="pane-notRehirable" aria-expanded="true">
                                <div id="gridNotRehirable"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
