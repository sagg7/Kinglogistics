<x-app-layout>
    <x-slot name="crumb_section">Job</x-slot>
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
                    tableProperties = (type) => {
                        const nameFormatter = (params) => {
                            if (params.value)
                                return params.value.name;
                            else
                                return '';
                        };
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name'},
                                {headerName: 'Zone', field: 'zone', valueFormatter: nameFormatter},
                                    @if(auth()->guard('web')->check())
                                {headerName: 'Customer', field: 'shipper', valueFormatter: nameFormatter},
                                @endif
                            ],
                            menu: [
                                @if(auth()->user()->can(['update-job']))
                                {text: 'Edit', route: '/trip/edit', icon: 'feather icon-edit'},
                                @endif
                                @if(auth()->user()->can(['delete-job']))
                                {route: '/trip/delete', type: 'delete'}
                                @endif
                            ],
                            container: `grid${tableName}`,
                            url: `/trip/search/${type}`,
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

            <div class="card-header">
                <a href="/trip/create" class="btn btn-primary">Create Job</a>
            </div>
            <hr>

            <div class="card-body">
                <div class="row ml-0">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-active" aria-expanded="true">
                                    <i class="fas fa-map mr-50 font-medium-3"></i>
                                    Active
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-inactive" aria-expanded="false">
                                    <i class="fas fa-trash mr-50 font-medium-3"></i>
                                    Inactive
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-active" aria-labelledby="pane-active" aria-expanded="true">
                                <div id="gridActive"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-inactive" aria-labelledby="pane-inactive" aria-expanded="true">
                                <div id="gridInactive"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
