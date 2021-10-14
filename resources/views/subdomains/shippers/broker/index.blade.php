<x-app-layout>
    <x-slot name="crumb_section">Staff</x-slot>
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
                const initTableStaff = () => {
                    if (tbAG)
                        return false;
                    const getRole = (params) => {
                        if (params.data)
                            return params.data.roles[0].name;
                    };
                    function PhoneCallRenderer() {}
                    PhoneCallRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        if (params.value) {
                            this.eGui.innerHTML = `<a href="tel:${params.value}">${params.value}</a>`;
                        }
                    }
                    PhoneCallRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    function MailToRenderer() {}
                    MailToRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        if (params.value) {
                            this.eGui.innerHTML = `<a href="mailto:${params.value}">${params.value}</a>`;
                        }
                    }
                    MailToRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    tbAG = new tableAG({
                        columns: [
                            //{headerName: 'Fecha', field: 'date'},
                            {headerName: 'Name', field: 'name'},
                            {headerName: 'Role', field: 'role', valueFormatter: getRole},
                            {headerName: 'Email', field: 'email', cellRenderer: MailToRenderer},
                            {headerName: 'Phone', field: 'phone', cellRenderer: PhoneCallRenderer},
                        ],
                        gridOptions: {
                            PhoneCall: PhoneCallRenderer,
                            MailTo: MailToRenderer,
                            undoRedoCellEditing: true,
                            onCellEditingStopped: function (event) {
                                if (event.value === '' || typeof event.value === "undefined") {
                                    tbActive.gridOptions.api.undoCellEditing();
                                    return;
                                }
                                const formData = new FormData();
                                formData.append(event.colDef.field, event.value);
                            },
                        },
                        container: 'gridStaff',
                        url: '/company/searchStaff',
                        tableRef: 'tbAG',
                        successCallback: (res) => {
                            res.rows.forEach((item) => {
                                item.area = item.roles.name;
                            });
                        }
                    });
                }
                options.click((e) => {
                    const link = $(e.currentTarget).find('a');
                    const id = link.attr('href');
                    const type = id.split('-')[1];
                    let pane = '';
                    switch (type) {
                        case 'company':
                            break;
                        case 'staff':
                            initTableStaff();
                            break;
                        case 'equipment':
                            pane = $('#pane-equipment');
                            if (pane.is(':empty'))
                                $.ajax({
                                    url: '/company/equipment',
                                    type: 'GET',
                                    success: (res) => {
                                        pane.html(`<h1>${res.title}</h1>${res.html}`);
                                    },
                                    error: () => {
                                        throwErrorMsg();
                                    }
                            });
                            break;
                        case 'services':
                            pane = $('#pane-services');
                            $.ajax({
                                url: '/company/services',
                                type: 'GET',
                                success: (res) => {
                                    pane.html(`<h1>${res.title}</h1>${res.html}`);
                                },
                                error: () => {
                                    throwErrorMsg();
                                }
                            });
                            break;
                    }
                });
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
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-company" aria-expanded="true">
                                    Company
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-staff" aria-expanded="false">
                                    Staff
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-equipment" aria-expanded="false">
                                    Equipment
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-services" aria-expanded="false">
                                    Services
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-company">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-content">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <colgroup>
                                                        <col style="width: 33.33%">
                                                        <col style="width: 33.33%">
                                                        <col style="width: 33.33%">
                                                    </colgroup>
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th>Company Name</th>
                                                        <th>Contact phone</th>
                                                        <th>Email</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td><p>{{ $broker->name ?? null }}</p></td>
                                                        <td>@isset($broker->contact_phone)<p><a href="tel:{{ $broker->contact_phone }}">{{ $broker->contact_phone }}</a></p>@endif</td>
                                                        <td>@isset($broker->email)<p><a href="mailto:{{ $broker->email }}">{{ $broker->email }}</a></p>@endif</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <colgroup>
                                                        <col style="width: 33.33%">
                                                        <col style="width: 33.33%">
                                                        <col style="width: 33.33%">
                                                    </colgroup>
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th>DOT number</th>
                                                        <th>MC number</th>
                                                        <th>Insurance</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td><p>{{ $broker->dot_number ?? null }}</p></td>
                                                        <td><p>{{ $broker->mc_number ?? null }}</p></td>
                                                        <td>
                                                            <p>
                                                                @isset($broker->insurance_url)
                                                                    <a class="d-block" href="{{ $broker->insurance_url }}" target="_blank">{{ $broker->insurance_file_name }}</a>
                                                                @endisset
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-staff">
                                <div id="gridStaff"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-equipment"></div>
                            <div role="tabpanel" class="tab-pane" id="pane-services"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
