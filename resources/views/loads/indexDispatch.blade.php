<x-app-layout>
    <x-slot name="crumb_section">Dispatch</x-slot>
    <x-slot name="crumb_subsection">Dashboard</x-slot>

    @section('head')
        <style>
        #morningTable th, #nightTable th {
            padding: 5px;
        }
        </style>
    @endsection
    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-photo", "title" => "Photo"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
        @include("common.modals.uploadXlsModal", ["id" => "compareLoadsModal", "title" => "Compare Loads","route"=>"load.uploadCompareLoadExcel","idForm"=>"uploadLoadForm"])
        @include("common.modals.genericAjaxLoading", ["id" => "AddObservation", "title" => "Load Observation", "content" =>
        Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']).
        Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5, 'maxlength' => 512]),
        'footerButton' => '<button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" id="sendObservation">Submit</button>'])
        @include("common.modals.genericAjaxLoading", ["id" => "transferJob", "title" => "Transfer Job", "content" =>
        Form::label('jobs', ucfirst(__('jobs')), ['class' => 'col-form-label']).
        Form::select('jobs', $jobs, null, ['class' => 'form-control select2']),
        'footerButton' => '<button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" id="transferJobButton">Submit</button>'])
        @include("loads.common.modals.driverStatus")
        @include("loads.common.modals.createDispatchReport")
        @include("loads.common.modals.DispatchReportModal")
        @include("common.modals.genericAjaxLoading", ["id" => "showDispatchReport", "title" => "Dispatch Report"])
        @include("loads.common.modals.createLoad")
        @include("loads.common.modals.viewOrigins")
        @include("loads.common.modals.viewDestinations")
        @include("loads.common.modals.resultsCompareLoadsModal")
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
        <link href="{{ asset('css/loadDispatch.css') }}" rel="stylesheet">
        <link href="{{ asset('js/modules/slim/slim.min.css') }}" rel="stylesheet">
    @endsection
    @section("scripts")
        <script src="{{ asset('js/modules/slim/slim.kickstart.min.js') }}"></script>
        @include("layouts.ag-grid.js")
        <script defer>
            let tbLoadActive = null;
            let tbLoadFinished = null;
            let now = null;
            let loadId = null;
            let matched = null;
            let internal = null;
            let external = null;
            let externalCount = 0;
            let internalCount = 0;
            let matchedCount = 0;
            (() => {
                let reference = {};
                let control = {};
                let bol = {};

                const handleRequest = (xhr) => {
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.setRequestHeader('csrf-token', '{{ csrf_token() }}');
                }

                const initUpload = () => {
                    $('input[type=file]').change((e) => {
                        const target = e.currentTarget,
                            inp = $(target),
                            label = inp.closest('label').find('.file-name'),
                            group = inp.closest('.file-group'),
                            rmvBtn = group.find('.remove-file'),
                            file = target.files[0];
                        if (file) {
                            label.text(file.name);
                            group.addClass('input-group');
                            rmvBtn.removeClass('d-none');
                        } else {
                            label.text('Upload File');
                            group.removeClass('input-group');
                            rmvBtn.addClass('d-none');
                        }
                    });
                    $('.remove-file').click((e) => {
                        const btn = $(e.currentTarget),
                            group = btn.closest('.file-group'),
                            inp = group.find('input[type=file]');
                        inp.val('').trigger('change');
                    });

                    const table = $('#file-uploads'),
                        tbody = table.find('tbody');
                    $('#replace-form').submit((e) => {
                        e.preventDefault();
                        const form = $(e.currentTarget),
                            url = form.attr('action');
                        let formData = new FormData(form[0]);
                        const btn = $(e.originalEvent.submitter),
                            btnText = btn.text();
                        btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`);
                        btn.prop('disabled', true);
                        $.ajax({
                            url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: (res) => {
                                if (res.success) {
                                    throwErrorMsg("Image replaced Correctly", {"title": "Success!", "type": "success", "redirect": "{{url('load/index')}}"})
                                } else
                                    throwErrorMsg();
                            },
                            error: () => {
                                throwErrorMsg();
                            }
                        }).always(() => {
                            btn.text(btnText).prop('disabled', false);
                        });
                    });


                }

                $('#view-photo').on('show.bs.modal', function(e) {
                    const modal = $(e.currentTarget),
                        modalBody = modal.find('.modal-body'),
                        content = modal.find('.content-body'),
                        anchor = $(e.relatedTarget),
                        modalSpinner = modalBody.find('.modal-spinner'),
                        img = anchor.find('img');
                        content.html(`
                            <div class="slim" id="editImg"
                                 data-service="{{ url('load/replacePhoto') }}/${img.attr('customid')}"
                                 data-fetcher="fetch.php"
                                 data-ratio="3:2"
                                 data-push="true"
                                 data-download="true"
                                 data-size="600,400"
                                 data-max-file-size="2">
                                <input type="file" name="slim[]"/>
                            </div>`);
                    modalSpinner.addClass('d-none');
                    content.removeClass('d-none');
                    initUpload();

                    const cropper = new Slim(document.getElementById('editImg'),{
                        crop: {
                            x: 0,
                            y: 0,
                            width: 100,
                            height: 200
                        },
                        service: `{{ url('load/replacePhoto') }}/${img.attr('customid')}`,
                        download: true,
                        willSave: function(data, ready) {
                            ready(data);
                        },
                        willRequest : handleRequest,
                        label: 'Drop your image here.',
                        buttonConfirmLabel: 'Ok'
                    });

                    $.ajax({
                        url: "{{ url('load/loadPhoto/') }}/"+img.attr('customid'),
                        type: 'POST',
                        success: (res) => {
                            cropper.load(res);
                        },
                        error: () => {
                            // ajaxAlert('Ocurrió un error procesando la operación');
                        }
                    });

                });
               /* window.Echo.private('load-status-update') //fix this
                    .listen('LoadUpdate', res => {
                        if (tbLoad) {
                            const find = tbLoad.dataSource.data.rows.find(obj => Number(obj.id) === Number(res.load.id));
                            if (find) {
                                const frontData = new FrontDataSource({load: res.load});
                                tbLoad.gridOptions.api.setServerSideDatasource(frontData);
                            }
                        }
                    });*/

                const dateRange = $('#dateRange');
                dateRange.daterangepicker({
                    format: 'YYYY/MM/DD',
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                }, (start, end, label) => {
                    tbLoadActive.searchQueryParams = _.merge(
                        tbLoadActive.searchQueryParams,
                        {
                            start: start.format('YYYY/MM/DD'),
                            end: end.format('YYYY/MM/DD'),
                        });
                    tbLoadActive.updateSearchQuery();
                    tbLoadFinished.searchQueryParams = _.merge(
                        tbLoadFinished.searchQueryParams,
                        {
                            start: start.format('YYYY/MM/DD'),
                            end: end.format('YYYY/MM/DD'),
                        });
                    tbLoadFinished.updateSearchQuery();
                });

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
                    tbLoadActive.searchQueryParams = _.merge(
                        tbLoadActive.searchQueryParams,
                        {
                            shipper: e.params.data.id,
                        });
                    tbLoadActive.updateSearchQuery();
                    tbLoadFinished.searchQueryParams = _.merge(
                        tbLoadFinished.searchQueryParams,
                        {
                            shipper: e.params.data.id,
                        });
                    tbLoadFinished.updateSearchQuery();
                    filtersChange($('#customerTable'));
                }).on('select2:unselect', () => {
                    tbLoadFinished.searchQueryParams.shipper = null;
                    tbLoadFinished.updateSearchQuery();
                    tbLoadActive.searchQueryParams.shipper = null;
                    tbLoadActive.updateSearchQuery();
                    filtersChange($('#customerTable'));
                });

                let tableProperties = (type) => {
                    /*class FrontDataSource {
                        constructor(data) {
                            this.load = data.load;
                        }
                        getRows(params) {
                            let current = null;
                            if (type === 'active')
                                current = tbLoadActive.dataSource.data;
                            else
                                current = tbLoadFinished.dataSource.data;

                            const idx = current.rows.findIndex(obj => Number(obj.id) === Number(this.load.id));
                            if (idx) {
                                /*switch (this.load.status) {
                                    case 'finished':
                                        _.remove(current.rows, obj => Number(obj.id) === Number(this.load.id));
                                        break;
                                    default:
                                        break;
                                }/
                                current.rows[idx] = this.load;
                                params.successCallback(current.rows, current.lastRow);
                            } else {
                                current.rows.unshift(this.load);
                                params.successCallback(current.rows, current.lastRow);
                            }
                            return false;
                        }
                    }*/

                    const checkDuplicates = (numberType = null, current) => {
                        current.dataSource.data.rows.forEach(item => {
                            if (!numberType || numberType === 'control_number') {
                                if (item.control_number) {
                                    if (!control[item.control_number]) {
                                        control[item.control_number] = {
                                            count: 1,
                                            ids: [],
                                        };
                                    } else if (control[item.control_number]['ids'].find(obj => obj === item.id)) {
                                        return;
                                    } else
                                        control[item.control_number]['count']++;
                                    control[item.control_number]['ids'].push(item.id);
                                }
                            }
                            if (!numberType || numberType === 'customer_reference') {
                                if (item.customer_reference) {
                                    if (!reference[item.customer_reference]) {
                                        reference[item.customer_reference] = {
                                            count: 1,
                                            ids: [],
                                        };
                                    } else if (reference[item.customer_reference]['ids'].find(obj => obj === item.id)) {
                                        return;
                                    } else
                                        reference[item.customer_reference]['count']++;
                                    reference[item.customer_reference]['ids'].push(item.id);
                                }
                            }
                            if (!numberType || numberType === 'bol') {
                                if (item.bol) {
                                    if (!bol[item.bol]) {
                                        bol[item.bol] = {
                                            count: 1,
                                            ids: [],
                                        };
                                    } else if (bol[item.bol]['ids'].find(obj => obj === item.id)) {
                                        return;
                                    } else
                                        bol[item.bol]['count']++;
                                    bol[item.bol]['ids'].push(item.id);
                                }
                            }
                        });
                        if (!numberType || numberType === 'control_number') {
                            current.columnDefs[6].cellClass = params => {
                                if (params.value && control[params.value] && control[params.value]['count'] > 1)
                                    return 'bg-danger text-white';
                            }
                        }
                        if (!numberType || numberType === 'customer_reference') {
                            current.columnDefs[7].cellClass = params => {
                                if (params.value && reference[params.value] && reference[params.value]['count'] > 1)
                                    return 'bg-danger text-white';
                            }
                        }
                        if (!numberType || numberType === 'bol') {
                            current.columnDefs[8].cellClass = params => {
                                if (params.value && bol[params.value] && bol[params.value]['count'] > 1)
                                    return 'bg-danger text-white';
                            }
                        }
                        current.gridOptions.api.setColumnDefs(current.columnDefs);
                    }

                    const tableName = type.replace(/^\w/, (c) => c.toUpperCase());
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
                    function LoadTimeRenderer() {}
                    LoadTimeRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        if(!now)
                            now = new Date(params.now);

                        const created = new Date(params.value).getTime();
                        let nowT = now.getTime();

                        if (params.data.status === 'finished')
                            nowT = new Date(params.data.finished_timestamp).getTime();

                        let color = 'black';
                        if((nowT - created) > 4*1000*60*60 || params.data.status === 'unallocated' || params.data.status === 'requested' || params.data.status === 'accepted')
                            color = 'red'
                        let classU = "";
                        this.eGui.innerHTML = `<span class = "${classU}" time = "${nowT - created}" style="color: ${color}">${msToTime(nowT - created)}</span>`;
                        
                        if(params.data.status === 'finished'){
                            new bootstrap.Tooltip(this.eGui, {title: `Dispatch: ${params.data.user?params.data.user.name:''}`});
                        }else if (params.data.status !== "finished") {
                            new bootstrap.Tooltip(this.eGui, {title: `Dispatch: ${params.data.creator?params.data.creator.name:''}`});
                            classU = "update"
                            console.log(params.data.creator);
                        }
                        

                    }
                    LoadTimeRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    const emptyFormatter = (params) => {
                        if (params.value)
                            return params.value;
                        else
                            return 'ㅤ';
                    };
                    function JobRenderer() {}
                    JobRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        this.eGui.innerHTML = `<a onclick="transferJobModal(${params.data.id})">${params.value.name}</a>`;
                        new bootstrap.Tooltip(this.eGui, {title: `Destination / Loader - ${Math.round(params.data.mileage)} miles`});
                    }
                    JobRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    function PoRenderer() {}
                    PoRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        this.eGui.innerHTML = `${params.value}`;
                        new bootstrap.Tooltip(this.eGui, {title: `Load type - ${params.data.load_type.name}`});
                    }
                    PoRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    function statusRenderer() {}
                    statusRenderer.prototype.init = (params) => {
                        let string = params.value ? params.value : '';
                        if (string === "to_location")
                            string = "in transit";
                        string = string.charAt(0).toUpperCase()  + string.slice(1)
                        this.eGui = document.createElement('div');
                        this.eGui.innerHTML = `${params.value}`;
                        new bootstrap.Tooltip(this.eGui, {title: `${params.data.notes}`});
                    }
                    statusRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    function PhotosRenderer() {}
                    PhotosRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        if (params.value) {
                            let html = '',
                                photos = [],
                                photosId = [];
                            if (params.value.to_location_voucher_image_url){
                                photos.push(params.value.to_location_voucher_image_url);
                                photosId.push(params.value.load_id+'/to_location');
                            } else {
                                photos.push("{{url("images/app/nonupdated.png")}}");
                                photosId.push(params.value.load_id+'/to_location');
                            }
                            if (params.value.finished_voucher_image_url){
                                photos.push(params.value.finished_voucher_image_url);
                                photosId.push(params.value.load_id+'/finished');
                            } else {
                                photos.push("{{url("images/app/nonupdated.png")}}");
                                photosId.push(params.value.load_id+'/finished');
                            }
                            photos.forEach((item, index) => {
                                html += `<a class="avatar" href="#view-photo" data-toggle="modal" data-target="#view-photo"><img src="${item}" customid="${photosId[index]}" alt="photo" width="32" height="32"></a>`;
                            });
                            this.eGui.innerHTML = html;
                        }
                    }
                    PhotosRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    function StatusBarRenderer() {}
                    StatusBarRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        let colorClass;
                        if (params.value)
                            colorClass = 'bg-success';
                        else
                            colorClass = 'bg-warning';

                        this.eGui.innerHTML = `<div class="text-center ${colorClass} colors-container" style="width: 4px;">&nbsp;</div>`;
                    }
                    StatusBarRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    function DateRenderer() {}
                    DateRenderer.prototype.init = (params) => {
                        this.eGui = document.createElement('div');
                        let string = `<div class="text-center" style="line-height: 15px;"><i class="fas fa-arrow-circle-right"></i>${params.data.accepted_timestamp}<br>`;
                        if(params.data.finished_timestamp)
                            string += `<i class="fas fa-arrow-circle-left"></i>${params.value}</div>`;

                        this.eGui.innerHTML = string;
                        new bootstrap.Tooltip(this.eGui, {title: `→Accepted at \n ←Finished at`});
                    }
                    DateRenderer.prototype.getGui = () => {
                        return this.eGui;
                    }
                    const menu = [
                        @if(auth()->user()->can(['update-load-dispatch']))
                        {
                            text: 'Mark as inspected', route: '/load/markAsInspected', icon: 'feather icon-check-circle', type: 'confirm', conditional: 'inspected === null',
                            menuData: {
                                title: 'Confirm marking load as inspected?',
                                stopReloadOnConfirm: true,
                                afterConfirmFunction: (params) => {
                                    params.node.data.inspected = 1;
                                    params.api.redrawRows();
                                }
                            },
                        },
                        {
                            text: 'Unmark as inspected', route: '/load/unmarkAsInspected', icon: 'feather icon-x-circle', type: 'confirm', conditional: 'inspected !== null',
                            menuData: {
                                title: 'Confirm unmarking load as inspected?',
                                stopReloadOnConfirm: true,
                                afterConfirmFunction: (params) => {
                                    params.node.data.inspected = null;
                                    params.api.redrawRows();
                                }
                            }
                        },
                        {
                            text: 'Add Observations', route: '#AddObservation', icon: 'far fa-folder-open', type: 'modal'
                        },
                        {
                            text: 'Finish load', route: "/load/finishLoad", icon: 'feather icon-check-square', type: 'confirm', conditional: 'status != "finished"', menuData: {title: 'Are you sure you want to end this load?'}
                        },
                            @if(auth()->user()->can(['delete-load-dispatch']))
                        {
                            route: '/load/delete', type: 'delete'
                        },
                            @endif
                        @endif
                    ];
                    const gridOptions = {
                        undoRedoCellEditing: true,
                        onCellEditingStopped: function (event) {
                            let table = null;
                            if(type === 'active')
                                table = tbLoadActive;
                            else
                                table = tbLoadFinished;

                            checkDuplicates(event.colDef.field, table);
                            if (event.value === '' || typeof event.value === "undefined") {
                                table.gridOptions.api.undoCellEditing();
                                return;
                            }
                            const formData = new FormData();
                            formData.append(event.colDef.field, event.value);
                            $.ajax({
                                url: `/load/partialUpdate/${event.data.id}`,
                                type: 'POST',
                                data: formData,
                                contentType: false,
                                processData: false,
                                error: () => {
                                    table.gridOptions.api.undoCellEditing();
                                    throwErrorMsg();
                                }
                            });
                        },
                        components: {
                            OptionModalFunc: (modalId, load_id) => {
                                loadId = load_id;
                                const modal = $(`${modalId}`),
                                    content = modal.find('.content-body');
                                content.html('<div class="form-group col-12">'
                                    +'<label for="observations" class="col-form-label">Observations</label>'
                                    +'<textarea class="form-control" rows="5" maxlength="512" name="observations" cols="50" id="observations"></textarea>'
                                    +'</div>');
                                $.ajax({
                                    url: `/load/getLoadNote/${loadId}`,
                                    type: 'GET',
                                    success: (res) => {
                                        $('#observations').val(res)
                                    }
                                });
                                $('.modal-spinner').addClass('d-none');
                                modal.modal('show');
                            }
                        },
                    };
                    return {
                        columns: [
                            {headerName: '', field: 'inspected', filter: false, sortable: false, maxWidth: 14, cellRenderer: StatusBarRenderer},
                            {headerName: 'Timestamp', field: 'finished_timestamp', cellRenderer: DateRenderer},
                            {headerName: 'Truck #', field: 'truck', valueFormatter: truckFormatter},
                            {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                            {headerName: 'Carrier', field: 'driver.carrier', valueFormatter: nameFormatter},
                            {headerName: 'Photos', field: 'load_status', filter: false, cellRenderer: PhotosRenderer},
                            {headerName: 'Control #', field: 'control_number', editable: true, valueFormatter: emptyFormatter},
                            {headerName: 'C Reference', field: 'customer_reference', editable: true, valueFormatter: emptyFormatter},
                            {headerName: 'BOL', field: 'bol', editable: true, valueFormatter: emptyFormatter},
                            {headerName: 'Tons', field: 'tons', editable: true, valueFormatter: emptyFormatter},
                            {headerName: 'Job', field: 'trip', editable: false, cellRenderer: JobRenderer},
                            {headerName: 'PO', field: 'customer_po', editable: false, cellRenderer: PoRenderer},
                            {headerName: 'Customer', field: 'shipper', editable: false, valueFormatter: nameFormatter},
                            {headerName: 'Status', field: 'status', cellRenderer: statusRenderer},
                            {headerName: 'Load time', field: 'accepted_timestamp', cellRenderer: LoadTimeRenderer},
                        ],
                        menu,
                        gridOptions,
                        container: `grid${tableName}`,
                        url: `/load/search?type=${type}`,
                        tableRef: `tb${tableName}`,
                        successCallback: (params) => {
                            checkDuplicates(null, (type === 'active') ? tbLoadActive : tbLoadFinished );
                            now = new Date(params.now);
                        },
                        searchQueryParams: {
                            dispatch: 1,
                        }
                    };
                };

                tbLoadActive = new tableAG(tableProperties(`active`));
                tbLoadFinished = new tableAG(tableProperties(`finished`));

                setTimeout(() => {
                    addTime();
                }, 1000);

                $('#sendObservation').click(function (e) {
                    $.ajax({
                        url: `/load/addObservation/${loadId}`,
                        type: 'POST',
                        data: {
                            observation: $('#observations').val()
                        },
                        success: (res) => {
                            throwErrorMsg("The invoices are being created please wait a minute", {"title": "Success!", "type": "success"})
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    });
                    $("#AddObservation").modal("hide");
                });

                $('#transferJobButton').click(function (e) {
                    $.ajax({
                        url: `/load/transferJob/${loadId}`,
                        type: 'POST',
                        data: {
                            trip_id: $('#jobs').val()
                        },
                        success: (res) => {
                            throwErrorMsg("Job transfer succeeded", {"title": "Success!", "type": "success"});
                            tbLoadActive.updateSearchQuery();
                            tbLoadFinished.updateSearchQuery();
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    });
                    $("#transferJob").modal("hide");
                });


                $('#compareLoads').click(function (e) {
                    if(!$("#shipper").val()){
                        throwErrorMsg("You have to select a Customer");
                    } else {
                        $('#compareLoadsModal').modal('show');
                    }
                });


                //this area belong of option of menu to uploadXLS
                const uploadModal = $('#compareLoadsModal');
                const xlsInput = $('#fileExcel');
                xlsInput.change((e) => {
                    const target = e.currentTarget,
                        inp = $(target),
                        icon = inp.closest('label'),
                        form = inp.closest('form'),
                        btn = form.find('button[type=submit]'),
                        file = target.files[0];
                    if (file) {
                        icon.removeClass('bg-warning').addClass('bg-success');
                        btn.removeClass('btn-warning').addClass('btn-success')
                        .text(`Upload: ${file.name}`)
                        .prop('disabled', false);
                    } else {
                        icon.removeClass('bg-success').addClass('bg-warning');
                        btn.removeClass('btn-success').addClass('btn-warning')
                        .text('Upload')
                        .prop('disabled', true);
                    }
                });

                const resultsCompareLoadsModal = $('#resultsCompareLoadsModal');
                $('#uploadLoadForm').submit((e) => {
                    e.preventDefault();
                    let dateRange = $('#dateRange');
                    let shipper = $("#shipper").val();
                    const form = $(e.currentTarget),
                        url = form.attr('action');
                    let formData = new FormData(form[0]);
                    formData.append('dateRange', dateRange.val());
                    formData.append('shipper', $("#shipper").val());
                    $.ajax({
                        url,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: (res) => {
                            if (res.success) {
                                if (res.errors_file) {
                                    location.href = res.errors_file;
                                }
                                uploadModal.modal('hide');
                                matched = res.dataFile.columnsMatched;
                                external = res.dataFile.external;
                                idInternal = res.dataFile.idLoadsInternal;
                                matchedCount = matched.length;
                                externalCount = Object.keys(external).length;
                                internalCount = Object.keys(idInternal).length;
                                $('#matchedColumn').html(matchedCount);
                                $('#internalColumn').html(internalCount);
                                $('#externalColumn').html(externalCount);

                                    resultsCompareLoadsModal.modal('show');
                                    let externalUrl = 'array=' + JSON.stringify(external);

                                $('#buttonDownloadExternal').attr("onclick","downloadExternal("+externalUrl+")");
                                $('#buttonDownloadInternal').attr("onclick","downloadInternal(["+idInternal+"])");
                                $('#createLoadsFromExternal').attr("onclick","createLoadsExternalFunc(["+externalUrl+"])");


                            } else
                                throwErrorMsg();

                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    }).always(() => {
                        $('.ajax-loader').parent().prop('disabled',false).html('Upload');
                    });
                });


            })();


            function downloadInternal(internalInput) {
                if(internalCount > 1000){
                    throwErrorMsg("Error: You can not download more than 1000 loads.")
                }else{ window.location = "/load/downloadXLSInternal/internal?" + $.param({
                    array: JSON.stringify( internalInput )
                });}

                /*$.ajax({
                    url: '/load/downloadXLSInternal/internal',
                    type: 'POST',
                    data: {
                        array: JSON.stringify( internalInput )
                    },
                    success: (res) => {

                    }
                });*/
            }

            function downloadExternal(externalInput) {
                if(externalCount > 400){
                    throwErrorMsg("Error: You can not upload more than 400 loads.")
                }else{window.location = "/load/downloadXLSExternal/external?"+ $.param({
                    array: JSON.stringify( externalInput )
                });}

            }
            function createLoadsExternalFunc(externalInput) {
                if(externalCount > 400){
                    throwErrorMsg("Error: You can not upload more than 400 loads.")
                }else{
                    $.ajax({
                        url: '/load/createLoadsExternal/external',
                        type: 'GET',
                        data: {
                            array: JSON.stringify( externalInput ),
                            shipper: $("#shipper").val(),
                        },
                        success: (res) => {
                            if (res.success) {
                                throwErrorMsg("Load Generated Successfully", {"title": "Success!", "type": "success"});
                                $('#resultsCompareLoadsModal').modal('hide');
                                if (res.errors_file) {
                                    location.href = res.errors_file;
                                }
                            } else
                                throwErrorMsg();
                                if (res[0]) {
                                    location.href = res[0];
                                }
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    });
                }
            }

            function transferJobModal(id){
                loadId = id;
                $("#transferJob").modal("show");
            }



            function downloadDispatch() {
                window.location = "{{url("load/DownloadExcelReport")}}?" + $.param({
                    dateRange: dateRange.value,
                    shipper: $("#shipper").val(),
                });
                /*$.ajax({
                    url: "{{url("load/DownloadExcelReport")}}",
                    type: 'GET',
                    data: {
                        dateRange: dateRange.value,
                        shipper: $("#shipper").val(),
                    },
                    success: (res) => {
                        if (res.success)
                            window.location = '/jobOpportunity/index';
                        else
                            throwErrorMsg();
                    },
                    error: (res) => {
                        let errors = `<ul class="text-left">`;
                        Object.values(res.responseJSON.errors).forEach((error) => {
                            errors += `<li>${error}</li>`;
                        });
                        errors += `</ul>`;
                        throwErrorMsg(errors, {timer: false});
                    },
                });*/
            }

            function downloadLoads(type){
                window.location = "{{url("load/search")}}?" + $.param({
                    dateRange: dateRange.value,
                    shipper: $("#shipper").val(),
                    download: 1,
                    dispatch: 1,
                    type: type,
                    startRow : 0,
                    endRow: 1000,
                });
            }

            function openPicReport() {
                window.location = "{{url("load/pictureReport")}}?" + $.param({
                    dateRange: dateRange.value,
                    shipper: $("#shipper").val(),
                });
            }


            function addTime() {
                $(".update").each(function (index) {
                    let time = parseFloat($(this).attr('time')) + 1000;
                    $(this).html(msToTime(time));
                    $(this).attr('time', time);
                });
                setTimeout(() => {
                    addTime();
                }, 1000);
            }

            function addObservation($id){
                loadId = $id;
                $('#AddObservation').modal('show');
            }

            function msToTime(duration, showSeconds = true) {
                let seconds = Math.floor((duration / (1000)) % 60),
                    minutes = Math.floor((duration / (1000 * 60)) % 60),
                    hours = Math.floor((duration / (1000 * 60 * 60)) % 24),
                    days = Math.floor((duration / (1000 * 60 * 60 * 24)));

                hours = (hours < 10) ? "0" + hours : hours;
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                let secs = "";
                if (showSeconds)
                    secs = seconds + " s";
                if (days > 0)
                    return days + " d " + hours + " h " + minutes + " m " + secs;
                else if (hours > 0)
                    return hours + " h " + minutes + " m " + secs;
                else
                    return minutes + " m " + secs;
            }


            const guard = 'web';
        </script>
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/dashboard/loadSummary.min.js') }}"></script>
        <script src="{{ asset('js/sections/loads/dispatch/loadSummary.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/loads/dispatch/driverStatus.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/sections/loads/dispatch/customerStatus.min.js') }}"></script>
        <script src="{{ asset('js/sections/loads/dispatch/createDispatchReport.min.js') }}"></script>
        <script src="{{ asset('js/sections/loads/dispatch/dispatchReport.min.js') }}"></script>
        <script src="{{ asset('js/sections/loads/common.min.js?1.0.4') }}"></script>
        <script src="{{ asset('js/sections/loads/dispatch/originsAndDestinations.min.js?1.0.0') }}"></script>
    @endsection
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#createLoadModal">Create load</button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#viewOriginsModal">View Origins</button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#viewDestinationsModal">View Destinations</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block waves-effect waves-light" onclick="getDispatchReport()">Dispatch Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body text-center">
                        <h3>Shift Truck Status</h3>
                        <button class="btn btn-block btn-outline-primary" type="button" data-toggle="modal"
                                data-target="#driverStatusModal" id="morning_dispatch">Morning</button>
                        <table class="table table-striped table-bordered mt-1" id="morningTable">
                            <thead>
                            <tr>
                                <th>Active</th>
                                <th>Inactive</th>
                                <th>Awaiting</th>
                                <th>Loaded</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                        <button class="btn btn-block btn-outline-primary" type="button" data-toggle="modal"
                                data-target="#driverStatusModal" id="night_dispatch">Night</button>
                        <table class="table table-striped table-bordered mt-1" id="nightTable">
                            <thead>
                            <tr>
                                <th>Active</th>
                                <th>Inactive</th>
                                <th>Awaiting</th>
                                <th>Loaded</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12" >
            <div class="card">
                <div class="card-content">
                    <div class="card-body text-center table-responsive" style="height:355px ">
                        <h3>Customer Status</h3>

                        <table class="table table-striped table-bordered mt-1" id="customerTable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>AVG Waiting Per Load</th>
                                <th>AVG Load Time</th>
                                <th>Truck Active Required</th>
                            </tr>
                            </thead>

                            <tbody >
                            <tr >
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.common.loadStatus', ['showFilters' => false])

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <fieldset class="form-group col-5">
                        {!! Form::label('dateRange', 'Select Dates', ['class' => 'col-form-label']) !!}
                        {!! Form::text('dateRange', null, ['class' => 'form-control']) !!}
                    </fieldset>

                    <fieldset class="form-group col-6">
                        {!! Form::label('shipper', 'Customer', ['class' => 'col-form-label']) !!}
                        {!! Form::select('shipper', [], null, ['class' => 'form-control', 'id'=>'shipper']) !!}
                    </fieldset>

                    <fieldset class="form-group col-1">
                        <div class="dropdown float-right">
                            <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                <a class="dropdown-item" id="genDisReport" onclick="fillFormDispatchReport()"><i class="fas fa-edit"></i> Generate Dispatch Report</a>
                                <a class="dropdown-item" id="completeAll" onclick="downloadDispatch()"><i class="fas fa-file-excel"></i> Download Dispatch Report</a>
                                <a class="dropdown-item" id="openPicReport" onclick="openPicReport()"><i class="fas fa-file-image"></i> Picture Report</a>
                                <a class="dropdown-item" id="compareLoads"><i class="fas fa-file-upload"></i>Compare Loads</a>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-right">
                <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                    <a class="dropdown-item" id="completeAll" onclick="downloadLoads('active')"><i class="fas fa-file-excel"></i> Download Active Loads</a>
                </div>
            </div>
            <div class="card-content">
                <h3>Active Loads</h3>
                <hr>

                <div id="gridActive"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
              <div class="dropdown float-right">
                    <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                        <a class="dropdown-item" id="completeAll" onclick="downloadLoads('finished')"><i class="fas fa-file-excel"></i> Download Finished Loads</a>
                    </div>
                </div>
            <div class="card-content">
                <h3>Finished Loads</h3>

                <hr>
                <div id="gridFinished"></div>
            </div>
        </div>
    </div>
</x-app-layout>
