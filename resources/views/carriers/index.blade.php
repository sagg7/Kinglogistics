<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section('vendorCSS')
        @include('layouts.ag-grid.css')
    @endsection
    @section('scripts')
        @include('layouts.ag-grid.js')
        <script defer>
            var tbActive = null,
                tbReady = null,
                tbProspects = null,
                tbDeleted = null,
                tbNotWorking = null,
                tbNotRehirable = null;
            let previousModalId = null;
            (() => {
                const pills = $('.nav-pills'),
                    options = pills.find('.nav-item'),
                    tableProperties = (type) => {
                        const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
                        let menu;
                        let gridOptions = {};
                        switch (type) {
                            default:
                                menu = [{
                                        text: 'Summary',
                                        route: '/carrier/show',
                                        icon: 'far fa-eye'
                                    },
                                    {
                                        text: 'Paperwork',
                                        route: '#view-paperwork',
                                        icon: 'far fa-folder-open',
                                        type: 'modal'
                                    },
                                    @if (auth()->user()->can(['update-carrier']))
                                        {text: 'Edit', route: '/carrier/edit', icon: 'feather icon-edit'},
                                    @endif
                                    @if (auth()->user()->can(['update-carrier-active']))
                                        {
                                        text: 'Prospect',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "prospect"},
                                        icon: 'fas fa-check-circle',
                                        type: 'confirm',
                                        conditional: 'status === "interested"',
                                        menuData: {
                                        title: 'Set status as prospect?',
                                        afterConfirmFunction: () => {
                                        if (tbProspects)
                                        tbProspects.updateSearchQuery();
                                        }
                                        },
                                        },
                                        {
                                        text: 'Ready to work',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "ready"},
                                        icon: 'fas fa-check-circle',
                                        type: 'confirm',
                                        conditional: 'status === "prospect"',
                                        menuData: {
                                        title: 'Set status as ready to work?',
                                        afterConfirmFunction: () => {
                                        if (tbReady)
                                        tbReady.updateSearchQuery();
                                        }
                                        },
                                        },
                                        {
                                        text: 'Active',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "active"},
                                        icon: 'fas fa-check-circle',
                                        type: 'confirm',
                                        conditional: 'status === "ready_to_work" || params.data.status === "not_working"',
                                        menuData: {
                                        title: 'Set status as active?',
                                        afterConfirmFunction: () => {
                                        if (tbActive)
                                        tbActive.updateSearchQuery();
                                        }
                                        },
                                        },
                                        {
                                        text: 'Not working',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "not_working"},
                                        icon: 'far fa-times-circle',
                                        type: 'confirm',
                                        conditional: 'status === "active"',
                                        menuData: {
                                        title: 'Set status as not working?',
                                        afterConfirmFunction: () => {
                                        if (tbNotWorking)
                                        tbNotWorking.updateSearchQuery();
                                        }
                                        },
                                        },
                                        {
                                        text: 'Not rehirable',
                                        route: "/carrier/setStatus",
                                        route_params: {status: "not_rehirable"},
                                        icon: 'fas fa-ban font-weight-bold',
                                        type: 'confirm',
                                        conditional: 'status === "active"',
                                        menuData: {
                                        title: 'Set status as not rehirable?',
                                        afterConfirmFunction: () => {
                                        if (tbNotRehirable)
                                        tbNotRehirable.updateSearchQuery();
                                        }
                                        },
                                        },
                                    @endif
                                    @if (auth()->user()->can(['delete-carrier']))
                                        {route: '/carrier/delete', type: 'delete'},
                                    @endif
                                    @if (auth()->user()->can(['read-carrier']))
                                        {text: 'View', route: '#viewCarriers', icon: 'far fa-eye', type: 'modal'},
                                    @endif
                                ];
                                gridOptions = {
                                    components: {
                                        OptionModalFunc: (modalId, carrierId) => {
                                            if (modalId == "#viewCarriers") {
                                                viewCarriersFunction(modalId, carrierId);
                                            } else {
                                                view_paperworkFunction(modalId, carrierId);
                                            }
                                        }
                                    },
                                };
                                break;
                            case "deleted":
                                menu = [
                                    @if (auth()->user()->can(['delete-carrier']))
                                        {text: 'Restore', route: '/carrier/restore', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm',
                                        menuData: {title: 'Restore carrier?'}}
                                    @endif
                                ];
                                break;
                            case "notRehirable":
                                menu = [
                                    @if (auth()->user()->can(['delete-carrier']))
                                        {text: 'Restore', route: '/carrier/restore', icon: 'fas fa-trash-restore font-weight-bold', type: 'confirm',
                                        menuData: {title: 'Restore carrier?'}}
                                    @endif
                                ];
                                break;
                        }
                        return {
                            columns: [{
                                    headerName: 'Name',
                                    field: 'name'
                                },
                                {
                                    headerName: 'Email',
                                    field: 'email'
                                },
                                {
                                    headerName: 'Phone',
                                    field: 'phone'
                                },
                            ],
                            menu,
                            gridOptions,
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

            function viewCarriersFunction(modalId, carrierId) {
                const modal = $(`${modalId}`),
                    content = modal.find('#viewCarrierContent');
                modal.modal('show');
                $.ajax({
                    type: 'GET',
                    url: '/carrier/show/' + carrierId,
                    success: (res) => {
                        // console.log(res.data);
                        content.empty();
                        content.append(        
                        `<div class="form-group">` +
                        `<div class="row">` +
                        `<div class="col-md-3">Name:</div>` +
                        ` <div class="col-md-3">${res.data.name}</div>` +
                        `<div class="col-md-3">Email:</div>` +
                        `<div class="col-md-3">${res.data.email}</div>` +
                        `</div><br>` +
                        // `<div class="row">` +
                        // `<div class="col-md-3">Password:</div>` +
                        // `<div class="col-md-3">${res.hours}</div>` +
                        // `<div class="col-md-3">Password Confirmation:</div>` +
                        // `<div class="col-md-3">${res.active_loads}</div>` +
                        // `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">Phone:</div>` +
                        `<div class="col-md-3">${res.data.phone}</div>` +
                        `<div class="col-md-3">Address:</div>` +
                        `<div class="col-md-3">${res.data.address}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">City:</div>` +
                        `<div class="col-md-3">${res.data.city}</div>` +
                        `<div class="col-md-3">State:</div>` +
                        `<div class="col-md-3">${res.data.state}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">Zip code</div>` +
                        `<div class="col-md-3">${res.data.zip_code}</div>` +
                        `<div class="col-md-3">Owner Name</div>` +
                        `<div class="col-md-3">${res.data.owner}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3"><p>Invoice Email:</p></div>` +
                        `<div class="col-md-3"><p>${res.data.invoice_email ?? null}</p></div>` +
                        `<div class="col-md-3"><p>Status:</p></div>` +
                        `<div class="col-md-3"><p>${res.data.status ?? null}</p></div>` +
                        `</div><br>`);

                    },
                    error: () => {
                        throwErrorMsg();
                    }
                });
            }

            function view_paperworkFunction(modalId, carrierId) {
                const modal = $(`${modalId}`),
                    content = modal.find('.content-body');
                modal.modal('show');
                if (carrierId === previousModalId)
                    return;
                previousModalId = carrierId;
                content.html('<h3>Paperwork</h3>' +
                    '<table class="table" id="paperworkTable"><thead><tr>' +
                    '<div class="progress progress-bar-primary progress-xl mt-1 mb-1">' +
                    '<div class="progress-bar" role="progressbar" id="paperworkProgress"></div>' +
                    '</div>' +
                    '<th>Status</th><th>Name</th><th>PDF</th>' +
                    '</tr></thead>' +
                    '<tbody></tbody></table><hr>' +
                    '<h3>File uploads</h3>' +
                    '<div class="progress progress-bar-primary progress-xl mt-1 mb-1">' +
                    '<div class="progress-bar" role="progressbar" id="uploadsProgress"></div>' +
                    '</div>' +
                    '<table class="table" id="uploadsTable"><thead><tr>' +
                    '<th>Status</th><th>Name</th><th>PDF</th><th>Expiration date</th>' +
                    '</tr></thead>' +
                    '<tbody></tbody></table><hr>'
                );
                const paperworkTable = content.find('#paperworkTable'),
                    paperworkTbody = paperworkTable.find('tbody'),
                    paperworkProgress = $('#paperworkProgress');
                const uploadsTable = content.find('#uploadsTable'),
                    uploadsTbody = uploadsTable.find('tbody'),
                    uploadsProgress = $('#uploadsProgress');
                $.ajax({
                    url: `/carrier/getCarrierData/${carrierId}`,
                    type: 'GET',
                    success: (res) => {
                        let totalProgress = 0,
                            completedProgress = 0;
                        res.filesTemplates.forEach((file, i) => {
                            if (file.required) {
                                totalProgress++;
                                if (res.paperworkTemplates[file.id])
                                    completedProgress++;
                            }
                            let iconClass;
                            if (res.paperworkTemplates[file.id]) {
                                iconClass =
                                    'icon-check-circle text-success';
                            } else if (file.required) {
                                iconClass =
                                    'icon-x-circle text-danger';
                            } else {
                                iconClass =
                                    'icon-alert-circle text-warning';
                            }
                            let pdfLink = '';
                            if (res.paperworkTemplates[file.id]) {
                                pdfLink =
                                    `<a href="/paperwork/pdf/${file.id}/${res.carrier.id}" target="_blank">Show PDF</a>`;
                            }
                            paperworkTbody.append(
                                `<tr data-file="${file.id}">` +
                                `<td><i class="feather ${iconClass}"></i></td>` +
                                `<td>${file.name}</td>` +
                                `<td>${pdfLink}</td>` +
                                `</tr>`);
                        });
                        let calculatedProgress = ((completedProgress *
                            100) / totalProgress).toFixed(2);
                        paperworkProgress.text(`${calculatedProgress}%`)
                            .css('width', `${calculatedProgress}%`);
                        totalProgress = 0;
                        completedProgress = 0;
                        res.filesUploads.forEach((file, i) => {
                            if (file.required) {
                                totalProgress++;
                                if (res.paperworkUploads[file.id])
                                    completedProgress++;
                            }
                            let iconClass;
                            if (res.paperworkUploads[file.id]) {
                                iconClass =
                                    'icon-check-circle text-success';
                            } else if (file.required) {
                                iconClass =
                                    'icon-x-circle text-danger';
                            } else {
                                iconClass =
                                    'icon-alert-circle text-warning';
                            }
                            let pdfLink = '';
                            let expiration = '';
                            if (res.paperworkUploads[file.id]) {
                                pdfLink =
                                    `<a href="/s3storage/temporaryUrl?url=${res.paperworkUploads[file.id].url}" target="_blank">${res.paperworkUploads[file.id].file_name}</a>`;
                                expiration = res.paperworkUploads[
                                        file.id].expiration_date ?
                                    res.paperworkUploads[file.id]
                                    .expiration_date : '';
                            }
                            let template = '';
                            if (file.file) {
                                template =
                                    `<a href="/s3storage/temporaryUrl?url=${file.file}" target="_blank">${file.file_name}</a>`;
                            }
                            uploadsTbody.append(
                                `<tr>` +
                                `<td class="file-icon"><i class="feather ${iconClass}"></i></td>` +
                                `<td>` +
                                `<div>${file.name}</div>` +
                                template +
                                `</td>` +
                                `<td>${pdfLink}</td>` +
                                `<td>` +
                                expiration +
                                `</td>` +
                                `</tr>`
                            );
                        });
                        calculatedProgress = ((completedProgress * 100) /
                            totalProgress).toFixed(2);
                        uploadsProgress.text(`${calculatedProgress}%`)
                            .css('width', `${calculatedProgress}%`);

                        content.removeClass('d-none');
                        $('.modal-spinner').addClass('d-none');
                    }
                });
            }
        </script>
    @endsection

    @section('modals')
        @include('common.modals.genericAjaxLoading', [
            'id' => 'view-paperwork',
            'title' => 'Paperwork progress',
        ])
        @include('carriers.common.modals.viewCarriers')
    @endsection

    <div class="card pills-layout">
        <div class="card-content">

            <div class="card-body">
                <div class="row ml-0">

                    <div class="col-lg col-md col-xs-12 col-sm-12 pl-0 pr-0 pills-menu-col">
                        <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 active" data-toggle="pill" href="#pane-active"
                                    aria-expanded="true">
                                    Active
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-ready"
                                    aria-expanded="true">
                                    Ready to work
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-prospect"
                                    aria-expanded="false">
                                    Prospects
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-deleted"
                                    aria-expanded="false">
                                    Deleted
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-notWorking"
                                    aria-expanded="false">
                                    Not working
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-notRehirable"
                                    aria-expanded="false">
                                    Not rehirable
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg col-md col-xs-12 col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pane-active" aria-labelledby="pane-active"
                                aria-expanded="true">
                                <div id="gridActive"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-ready" aria-labelledby="pane-ready"
                                aria-expanded="true">
                                <div id="gridReady"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-prospect"
                                aria-labelledby="pane-prospect" aria-expanded="true">
                                <div id="gridProspect"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-deleted" aria-labelledby="pane-deleted"
                                aria-expanded="true">
                                <div id="gridDeleted"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-notWorking"
                                aria-labelledby="pane-notWorking" aria-expanded="true">
                                <div id="gridNotWorking"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pane-notRehirable"
                                aria-labelledby="pane-notRehirable" aria-expanded="true">
                                <div id="gridNotRehirable"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
