<x-app-layout>
    <x-slot name="crumb_section">Drivers</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbMorning = null,
                tbNight = null,
                tbAwaiting = null,
                tbInactive = null;
            (() => {
                let now = null;
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    countActive = $('#count-active').find('span'),
                    countInactive = $('#count-inactive').find('span'),
                    countReady = $('#count-ready').find('span'),
                    countPending = $('#count-pending').find('span'),
                    countError = $('#count-error').find('span'),
                    countByTab = {},
                    setCount = (count) => {
                        countActive.text(count.active);
                        countInactive.text(count.inactive);
                        countReady.text(count.ready);
                        countPending.text(count.pending);
                        countError.text(count.error);
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
                        function TooltipRenderer() {}
                        TooltipRenderer.prototype.init = (params) => {
                            this.eGui = document.createElement('div');
                            this.eGui.id = `inactive_tooltip_${params.data.id}`;
                            this.eGui.innerHTML = params.value;
                            if (Number(params.data.inactive) === 1 && params.data.inactive_observations) {
                                new bootstrap.Tooltip(this.eGui, {title: params.data.inactive_observations});
                            }
                        }
                        TooltipRenderer.prototype.getGui = () => {
                            return this.eGui;
                        }
                        function StatusRenderer() {}
                        StatusRenderer.prototype.init = (params) => {
                            this.eGui = document.createElement('div');


                            if(params.value === 'pending'){
                                let created = new Date(params.data.bot_answer.updated_at).getTime();
                                let nowT = now.getTime();
                                let color = 'green';
                                if ((nowT - created) > 1000*60*10){
                                    if ((nowT - created) > 1000*60*20)
                                        color = 'red';
                                    else
                                        color = 'orange'
                                }
                                this.eGui.innerHTML = `<div class="text-center" style="color: ${color};">${params.value}</div>`;
                                new bootstrap.Tooltip(this.eGui, {title: msToTime(nowT - created)});
                            } else {
                                this.eGui.innerHTML = `<div class="text-center">${params.value}</div>`;
                            }

                        }
                        StatusRenderer.prototype.getGui = () => {
                            return this.eGui;
                        }
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name', cellRenderer: TooltipRenderer,},
                                {headerName: 'Zone', field: 'zone', valueFormatter: nameFormatter},
                                {headerName: '{{session('renames')->carrier ?? 'Carrier'}}', field: 'carrier', valueFormatter: nameFormatter},
                                {headerName: 'Load Status', field: 'latest_load', valueFormatter: capitalizeStatus},
                                {headerName: 'Status', field: 'status', cellRenderer: StatusRenderer},
                            ],
                            container: `grid${tableName}`,
                            url: `/driver/search/${type}`,
                            tableRef: `tb${tableName}`,
                            successCallback: (params) => {
                                if (params.count) {
                                    setCount(params.count);
                                    countByTab[type] = params.count;
                                }
                                if (params.now) {
                                    now = new Date(params.now);
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

            function msToTime(duration) {
                var minutes = Math.floor((duration / (1000 * 60)) % 60),
                    hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

                hours = (hours < 10) ? "0" + hours : hours;
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                if (hours > 0)
                    return hours + " hours " + minutes + " minutes";
                else
                    return minutes + " minutes";

            }
        </script>
    @endsection

    <div class="row text-center">
        <div class="col">
            <div class="card border-2 border-primary" id="count-active">
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
            <div class="card border-2 border-info" id="count-inactive">
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
            <div class="card border-2 border-success" id="count-ready">
                <div class="card-body">
                    <div class="card-content">
                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-check-double text-success font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="text-bold-700">Ready</h2>
                        <span class="font-large-1">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-2 border-warning" id="count-pending">
                <div class="card-body">
                    <div class="card-content">
                        <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="far fa-clock text-warning font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="text-bold-700">Pending</h2>
                        <span class="font-large-1">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-2 border-danger" id="count-error">
                <div class="card-body">
                    <div class="card-content">
                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fas fa-exclamation-triangle text-danger font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="text-bold-700">Error</h2>
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
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-inactive" aria-expanded="false">
                                    Inactive Down
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-deleted" aria-expanded="false">
                                    Deleted
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
                            <div role="tabpanel" class="tab-pane" id="pane-inactive" aria-labelledby="pane-inactive" aria-expanded="true">
                                <div id="gridInactive"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-deleted" aria-labelledby="pane-deleted" aria-expanded="true">
                                <div id="gridDeleted"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
