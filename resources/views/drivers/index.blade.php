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
                let globlalSearchQueryParams = null;
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
                            };
                        const truckFormatter = (params) => {
                            if (params.value)
                                return params.value.number;
                            else
                                return '';
                        };
                        const capitalizeFormatter = (value) => {
                            if (value)
                                return value.charAt(0).toUpperCase()  + value.slice(1);
                            else
                                return '';
                        };
                        const msToTime = (duration) => {
                            let minutes = Math.floor((duration / (1000 * 60)) % 60),
                                hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

                            hours = (hours < 10) ? "0" + hours : hours;
                            minutes = (minutes < 10) ? "0" + minutes : minutes;
                            if (hours > 0)
                                return hours + " hours " + minutes + " minutes";
                            else
                                return minutes + " minutes";

                        }
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
                        function StatusTooltip() {}
                        StatusTooltip.prototype.init = (params) => {
                            this.eGui = document.createElement('div');
                            this.eGui.id = `status_tooltip_${params.data.id}`;
                            let status;
                            if (!params.value || params.value.status === 'finished') {
                                status = 'No load assigned';
                            } else {
                                status = capitalizeFormatter(params.value.status);
                            }
                            this.eGui.innerHTML = status;
                            if (params.data.latest_load && params.data.latest_load.load_status) {
                                const finish_time = new Date(params.data.latest_load.load_status.finished_timestamp).getTime();
                                const nowT = now.getTime();
                                new bootstrap.Tooltip(this.eGui, {title: `Last Load: ${msToTime(nowT - finish_time)}`});
                            }
                        }
                        StatusTooltip.prototype.getGui = () => {
                            return this.eGui;
                        }
                        function StatusRenderer() {}
                        StatusRenderer.prototype.init = (params) => {
                            this.eGui = document.createElement('div');

                            const status = capitalizeFormatter(params.value);
                            if (params.value === 'pending') {
                                const created = params.data.bot_answer ? new Date(params.data.bot_answer.updated_at).getTime() : null;
                                const nowT = now.getTime();
                                let color = 'green';
                                if (created && (nowT - created) > 1000 * 60 * 10) {
                                    if ((nowT - created) > 1000 * 60 * 20)
                                        color = 'red';
                                    else
                                        color = 'orange'
                                }
                                this.eGui.innerHTML = `<div style="color: ${color};">${status}</div>`;
                                if (created)
                                    new bootstrap.Tooltip(this.eGui, {title: msToTime(nowT - created)});
                            } else {
                                this.eGui.innerHTML = `<div>${status}</div>`;
                            }

                        }
                        StatusRenderer.prototype.getGui = () => {
                            return this.eGui;
                        }
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        let menu;
                        if (type === "deleted") {
                            menu = [
                                @if(auth()->user()->can(['delete-driver']))
                                {text: 'Restore', route: '/driver/restore', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Restore driver?'}}
                                @endif
                            ];
                        } else {
                            menu = [
                                @if(auth()->user()->can(['update-driver']))
                                {text: 'Edit', route: '/driver/edit', icon: 'feather icon-edit'},
                                @endif
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operations') || auth()->user()->hasRole('dispatch'))
                                {
                                    text: 'End Shift',
                                    route: "/driver/endShift",
                                    icon: 'feather icon-x-circle',
                                    type: 'confirm', conditional: 'status === "pending" || params.data.status === "active" || params.data.status === "ready"',
                                    menuData: {title: "End driver's shift?"}
                                },
                                {
                                    text: 'Set as Active',
                                    route: "/driver/setActive",
                                    icon: 'fas fa-check-circle',
                                    type: 'confirm', conditional: 'status === "pending" || params.data.status === "ready"',
                                    menuData: {title: "Set driver as active?"}
                                },
                                @endif
                                @if(auth()->user()->can(['delete-driver']))
                                {route: '/driver/delete', type: 'delete'},
                                @endif
                            ];
                        }
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name', cellRenderer: TooltipRenderer,},
                                {headerName: 'Truck #', field: 'truck', valueFormatter: truckFormatter,},
                                {headerName: 'Zone', field: 'zone', valueFormatter: nameFormatter},
                                {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                                {headerName: 'Load Status', field: 'latest_load', cellRenderer: StatusTooltip},
                                {headerName: 'Status', field: 'status', cellRenderer: StatusRenderer},
                            ],
                            menu,
                            container: `grid${tableName}`,
                            url: `/driver/search/${type}`,
                            tableRef: `tb${tableName}`,
                            searchQueryParams: globlalSearchQueryParams,
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
                        const table = `tb${type.replace(/^\w/, (c) => c.toUpperCase())}`;
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

                const updateTablesParams = (params) => {
                    if (tbMorning) {
                        globlalSearchQueryParams = _.merge(tbMorning.searchQueryParams, params);
                        tbMorning.searchQueryParams = _.merge(tbMorning.searchQueryParams, params);
                        tbMorning.updateSearchQuery();
                    }
                    if (tbNight) {
                        tbNight.searchQueryParams = _.merge(tbNight.searchQueryParams, params);
                        tbNight.updateSearchQuery();
                    }
                    if (tbAwaiting) {
                        tbAwaiting.searchQueryParams = _.merge(tbAwaiting.searchQueryParams, params);
                        tbAwaiting.updateSearchQuery();
                    }
                    if (tbInactive) {
                        tbInactive.searchQueryParams = _.merge(tbInactive.searchQueryParams, params);
                        tbInactive.updateSearchQuery();
                    }
                }
                const clearTablesParams = (paramName) => {
                    if (tbMorning) {
                        paramName.forEach(name => {
                            tbMorning.searchQueryParams[name] = null;
                        });
                        tbMorning.updateSearchQuery();
                    }
                    if (tbNight) {
                        paramName.forEach(name => {
                            tbNight.searchQueryParams[name] = null;
                        });
                        tbNight.updateSearchQuery();
                    }
                    if (tbAwaiting) {
                        paramName.forEach(name => {
                            tbAwaiting.searchQueryParams[name] = null;
                        });
                        tbAwaiting.updateSearchQuery();
                    }
                    if (tbInactive) {
                        paramName.forEach(name => {
                            tbInactive.searchQueryParams[name] = null;
                        });
                        tbInactive.updateSearchQuery();
                    }
                }
                $('#shipper').select2({
                    ajax: {
                        url: '/shipper/selection',
                        data: (params) => {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                take: 15,
                            };
                        },
                    },
                    placeholder: 'Select',
                    allowClear: true,
                }).on('select2:select', (e) => {
                    updateTablesParams({shipper: e.params.data.id});
                }).on('select2:unselect', () => {
                    clearTablesParams(['shipper']);
                });
            })();
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
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <fieldset class="form-group col-6">
                        {!! Form::label('shipper', 'Customer', ['class' => 'col-form-label']) !!}
                        {!! Form::select('shipper', [], null, ['class' => 'form-control']) !!}
                    </fieldset>
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
