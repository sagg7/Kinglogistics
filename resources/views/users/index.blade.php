<x-app-layout>
    <x-slot name="crumb_section">Staff</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbActive = null;
            var tbCandidate = null;
            var tbRehirable = null;
            var tbNotRehirable = null;
            (() => {
                let getRole = (params) => {
                    if (params.data)
                        return params.data.roles[0].name;
                };
               
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    tableProperties = (type) => {
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        let menu;
                        let gridOptions = {};
                        let previousModalId = null;
                        switch (type) {
                            // default:
                                
                            //     break;
                            case "active":
                                menu = [
                                    @if(auth()->user()->can(['update-staff']))
                                    {text: 'Rehirable', route: '/user/rehirable', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Send Staff to Rehirable?'}},
                                    @endif
                                    @if(auth()->user()->can(['update-staff']))
                                    {text: 'Not Rehirable', route: '/user/notRehirable', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Send Staff to Not Rehirable?'}}
                                    @endif
                                    
                                ];
                                break;
                            case "candidate":
                                menu = [
                                    @if(auth()->user()->can(['update-staff']))
                                    {text: 'Activate', route: '/user/activate', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Send Staff to Activate?'}}
                                    @endif
                                ];
                                break;
                            case "rehirable":
                                menu = [
                                    @if(auth()->user()->can(['update-staff']))
                                    {text: 'Activate', route: '/user/activate', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Send Staff to Activate?'}}
                                    @endif
                                ];
                                break;
                            case "notRehirable":
                                menu = [
                                    @if(auth()->user()->can(['update-staff']))
                                    {text: 'Activate', route: '/user/activate', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm', menuData: {title: 'Send Staff to Activate?'}}
                                    @endif
                                ];
                                break;
                        }
                        menu.push(
                      				@if(auth()->user()->can(['update-staff']))
                      				{text: 'Edit', route: '/user/edit', icon: 'feather icon-edit'},
                      				@endif
                      				@if(auth()->user()->can(['delete-staff']))
                      				{route: '/user/delete', type: 'delete'}
                      				@endif
                    			);
                        return {
                            columns: [
                                {headerName: 'Name', field: 'name'},
                        		{headerName: 'Role', field: 'role', valueFormatter: getRole},
                        		{headerName: 'Email', field: 'email'},
                        		{headerName: 'Phone', field: 'phone'},
                            ],
                            menu,
                            gridOptions,
                            container: `grid${tableName}`,
                            url: `/user/search/${type}`,
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
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-active" aria-expanded="true">
                                    Active
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-candidate" aria-expanded="true">
                                    Candidate
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 " data-toggle="pill" href="#pane-rehirable" aria-expanded="true">
                                   Rehirable
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 " data-toggle="pill" href="#pane-notRehirable" aria-expanded="true">
                                   Not Rehirable
                                </a>
                            </li>
                          
                           
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-active" aria-labelledby="pane-active" aria-expanded="true">
                                <div id="gridActive"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane " id="pane-candidate" aria-labelledby="pane-candidate" aria-expanded="true">
                                <div id="gridCandidate"></div>
                            </div>
                           
                            <div role="tabpanel" class="tab-pane " id="pane-rehirable" aria-labelledby="pane-rehirable" aria-expanded="true">
                                <div id="gridRehirable"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane " id="pane-notRehirable" aria-labelledby="pane-notRehirable" aria-expanded="true">
                                <div id="gridNotRehirable"></div>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div></x-app-layout>
