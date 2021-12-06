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
                        let gridOptions = {};
                        let previousModalId = null;
                        switch (type) {
                            default:
                                menu = [
                                    {text: 'Paperwork', route: '#view-paperwork', icon: 'far fa-eye', type: 'modal'},
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
                                gridOptions = {
                                    components: {
                                        OptionModalFunc: (modalId, carrierId) => {
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
                                                            iconClass = 'icon-check-circle text-success';
                                                        } else if (file.required) {
                                                            iconClass = 'icon-x-circle text-danger';
                                                        } else {
                                                            iconClass = 'icon-alert-circle text-warning';
                                                        }
                                                        let pdfLink = '';
                                                        if (res.paperworkTemplates[file.id]) {
                                                            pdfLink = `<a href="/paperwork/pdf/${file.id}/${res.carrier.id}" target="_blank">Show PDF</a>`;
                                                        }
                                                        paperworkTbody.append(`<tr data-file="${file.id}">` +
                                                        `<td><i class="feather ${iconClass}"></i></td>` +
                                                        `<td>${file.name}</td>` +
                                                        `<td>${pdfLink}</td>` +
                                                        `</tr>`);
                                                    });
                                                    let calculatedProgress = ((completedProgress * 100) / totalProgress).toFixed(2);
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
                                                            iconClass = 'icon-check-circle text-success';
                                                        } else if (file.required) {
                                                            iconClass = 'icon-x-circle text-danger';
                                                        } else {
                                                            iconClass = 'icon-alert-circle text-warning';
                                                        }
                                                        let pdfLink = '';
                                                        let expiration = '';
                                                        if (res.paperworkUploads[file.id]) {
                                                            pdfLink = `<a href="/s3storage/temporaryUrl?url=>${res.paperworkUploads[file.id].url}" target="_blank">${res.paperworkUploads[file.id].file_name}</a>`;
                                                            expiration = res.paperworkUploads[file.id].expiration_date ? res.paperworkUploads[file.id].expiration_date : '';
                                                        }
                                                        let template = '';
                                                        if (file.file) {
                                                            template = `<a href="/s3storage/temporaryUrl?url=${file.file}" target="_blank">${file.file_name}</a>`;
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
                                                    calculatedProgress = ((completedProgress * 100) / totalProgress).toFixed(2);
                                                    uploadsProgress.text(`${calculatedProgress}%`)
                                                        .css('width', `${calculatedProgress}%`);

                                                    content.removeClass('d-none');
                                                    $('.modal-spinner').addClass('d-none');
                                                }
                                            });
                                        }
                                    },
                                };
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
        </script>
    @endsection

    <!-- TODO: ADD DOCUMENT PROGRESS MODAL -->
    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-paperwork", "title" => "Paperwork progress"])
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
                                <a class="nav-link d-flex py-75" data-toggle="pill" href="#pane-prospect" aria-expanded="false">
                                    Prospects
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
                            <div role="tabpanel" class="tab-pane active" id="pane-active" aria-labelledby="pane-active" aria-expanded="true">
                                <div id="gridActive"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="pane-prospect" aria-labelledby="pane-prospect" aria-expanded="true">
                                <div id="gridProspect"></div>
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
