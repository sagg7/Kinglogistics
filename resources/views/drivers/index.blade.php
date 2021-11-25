<x-app-layout>
    <x-slot name="crumb_section">Drivers</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbActive = null,
                tbInactive = null;
            (() => {
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    countActive = $('#count-active').find('span'),
                    countInactive = $('#count-inactive').find('span'),
                    countPending = $('#count-pending').find('span'),
                    countByTab = {},
                    setCount = (count) => {
                        countActive.text(count.active);
                        countInactive.text(count.inactive);
                        countPending.text(count.pending);
                    },
                    tableProperties = (type) => {
                        const nameFormatter = (params) => {
                                if (params.value)
                                    return params.value.name;
                                else
                                    return '';
                            },
                            capitalizeStatus = (params) => {
                                let string = params.value ? params.value.status : 'No load assigned';
                                if (string === "to_location")
                                    string = "in transit";
                                return string.charAt(0).toUpperCase()  + string.slice(1)
                            };
                        const capitalizeFormatter = (params) => {
                            if (params.value)
                                return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                            else
                                return '';
                        };
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name'},
                                {headerName: 'Zone', field: 'zone', valueFormatter: nameFormatter},
                                {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                                {headerName: 'Load Status', field: 'latest_load', valueFormatter: capitalizeStatus},
                                {headerName: 'Status', field: 'status', valueFormatter: capitalizeFormatter},
                                /*{
                                    headerName: 'Shift', field: 'shift',
                                    filter: false,
                                    sortable: false,
                                    valueFormatter: (params) => {
                                        return params.value ? 'Active' : 'Inactive';
                                    }
                                },*/
                            ],
                            menu: [
                                {text: 'Edit', route: '/driver/edit', icon: 'feather icon-edit'},
                                {route: '/driver/delete', type: 'delete'}
                            ],
                            container: `grid${tableName}`,
                            url: `/driver/search/${type}`,
                            tableRef: `tb${tableName}`,
                            successCallback: (params) => {
                                if (params.count) {
                                    setCount(params.count);
                                    countByTab[type] = params.count;
                                }
                            }
                        };
                    },
                    initTables = (type) => {
                        let table = `tb${type.replace(/^\w/, (c) => c.toUpperCase())}`;
                        if (!window[table])
                            window[table] = new tableAG(tableProperties(`${type}`));
                    },
                    activePane = (id) => {
                        const type = id.split('-')[1];
                        if (countByTab[type])
                            setCount(countByTab[type]);
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

    <div class="row text-center">
        <div class="col">
            <div class="card border-primary" id="count-active">
                <div class="card-body">
                    <div class="card-content">
                        <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-check text-primary font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="text-bold-700">Active</h2>
                        <span class="font-large-1">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-info" id="count-inactive">
                <div class="card-body">
                    <div class="card-content">
                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-times text-info font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="text-bold-700">Inactive</h2>
                        <span class="font-large-1">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-warning" id="count-pending">
                <div class="card-body">
                    <div class="card-content">
                        <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-exclamation text-warning font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="text-bold-700">Pending</h2>
                        <span class="font-large-1">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card pills-layout">
        <div class="card-content">

            <div class="card-body">
                <div class="row ml-0">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-morning" aria-expanded="true">
                                    Morning shift
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-night" aria-expanded="false">
                                    Night shift
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-awaiting" aria-expanded="false">
                                    Awaiting
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-morning" aria-labelledby="pane-morning" aria-expanded="true">
                                <div id="gridMorning"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-night" aria-labelledby="pane-night" aria-expanded="true">
                                <div id="gridNight"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-awaiting" aria-labelledby="pane-awaiting" aria-expanded="true">
                                <div id="gridAwaiting"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
