<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-photo", "title" => "Photo"])
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
        <link href="{{ asset('js/modules/slim/slim.min.css') }}" rel="stylesheet">
    @endsection
    @section("scripts")
        <script src="{{ asset('js/modules/slim/slim.kickstart.min.js') }}"></script>
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.0') }}"></script>
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/laravel-echo/echo.js') }}"></script>
        <script defer>
            var tbLoad = null;
            (() => {
                class FrontDataSource {
                    constructor(data) {
                        this.load = data.load;
                    }
                    getRows(params) {
                        const current = tbLoad.dataSource.data;
                        const idx = current.rows.findIndex(obj => Number(obj.id) === Number(this.load.id));
                        if (idx) {
                            /*switch (this.load.status) {
                                case 'finished':
                                    _.remove(current.rows, obj => Number(obj.id) === Number(this.load.id));
                                    break;
                                default:
                                    break;
                            }*/
                            current.rows[idx] = this.load;
                            params.successCallback(current.rows, current.lastRow);
                        } else {
                            current.rows.unshift(this.load);
                            params.successCallback(current.rows, current.lastRow);
                        }
                        return false;
                    }
                }
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const emptyFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return 'ㅤ';
                };
                const capitalizeStatus = (params) => {
                    let string = params.value ? params.value : '';
                    if (string === "to_location")
                        string = "in transit";
                    return string.charAt(0).toUpperCase()  + string.slice(1)
                };
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
                let reference = {};
                let control = {};
                let bol = {};
                const checkDuplicates = (type = null) => {
                    if (!type || type === 'control_number')
                        control = {};
                    if (!type || type === 'customer_reference')
                        reference = {};
                    if (!type || type === 'bol')
                        bol = {};
                    tbLoad.dataSource.data.rows.forEach(item => {
                        if (!type || type === 'control_number') {
                            if (item.control_number)
                                if (!control[item.control_number])
                                    control[item.control_number] = 1;
                                else
                                    control[item.control_number]++;
                        }
                        if (!type || type === 'customer_reference') {
                            if (item.customer_reference)
                                if (!reference[item.customer_reference])
                                    reference[item.customer_reference] = 1;
                                else
                                    reference[item.customer_reference]++;
                        }
                        if (!type || type === 'bol') {
                            if (item.bol)
                                if (!bol[item.bol])
                                    bol[item.bol] = 1;
                                else
                                    bol[item.bol]++;
                        }
                    });
                    if (!type || type === 'control_number') {
                        tbLoad.columnDefs[4].cellClass = params => {
                            if (params.value && control[params.value] > 1)
                                return 'bg-danger text-white';
                        }
                    }
                    if (!type || type === 'customer_reference') {
                        tbLoad.columnDefs[5].cellClass = params => {
                            if (params.value && reference[params.value] > 1)
                                return 'bg-danger text-white';
                        }
                    }
                    if (!type || type === 'bol') {
                        tbLoad.columnDefs[6].cellClass = params => {
                            if (params.value && bol[params.value] > 1)
                                return 'bg-danger text-white';
                        }
                    }
                    tbLoad.gridOptions.api.setColumnDefs(tbLoad.columnDefs);
                }
                tbLoad = new tableAG({
                    columns: [
                        {headerName: '', field: 'inspected', filter: false, sortable: false, maxWidth: 14, cellRenderer: StatusBarRenderer},
                        {headerName: 'Accepted at', field: 'accepted_timestamp'},
                        {headerName: 'Finished at', field: 'finished_timestamp'},
                        {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                        {headerName: 'Photos', field: 'load_status', filter: false, cellRenderer: PhotosRenderer},
                        {headerName: 'Control #', field: 'control_number', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'Customer Reference', field: 'customer_reference', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'BOL', field: 'bol', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'Status', field: 'status', valueFormatter: capitalizeStatus},
                    ],
                    menu: [
                        {text: 'Mark as inspected', route: '/load/markAsInspected', icon: 'feather icon-check-circle', type: 'confirm', conditional: 'inspected === null', menuData: {title: 'Confirm marking load as inspected?'}},
                    ],
                    gridOptions: {
                        PhotosRenderer: PhotosRenderer,
                        StatusBarRenderer: StatusBarRenderer,
                        undoRedoCellEditing: true,
                        onCellEditingStopped: function (event) {
                            checkDuplicates(event.colDef.field);
                            if (event.value === '' || typeof event.value === "undefined") {
                                tbLoad.gridOptions.api.undoCellEditing();
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
                                    tbLoad.gridOptions.api.undoCellEditing();
                                    throwErrorMsg();
                                }
                            });
                        },
                    },
                    container: 'myGrid',
                    url: '/load/search',
                    tableRef: 'tbLoad',
                    successCallback: (params) => {
                        checkDuplicates();
                    }
                });

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

                    var cropper = new Slim(document.getElementById('editImg'),{
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
                window.Echo.private('load-status-update')
                    .listen('LoadUpdate', res => {
                        if (tbLoad) {
                            const find = tbLoad.dataSource.data.rows.find(obj => Number(obj.id) === Number(res.load.id));
                            if (find) {
                                const frontData = new FrontDataSource({load: res.load});
                                tbLoad.gridOptions.api.setServerSideDatasource(frontData);
                            }
                        }
                    });

                const dateRange = $('#dateRange');
                dateRange.daterangepicker({
                    format: 'YYYY/MM/DD',
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                }, (start, end, label) => {
                    tbLoad.searchQueryParams = _.merge(
                        tbLoad.searchQueryParams,
                        {
                            start: start.format('YYYY/MM/DD'),
                            end: end.format('YYYY/MM/DD'),
                        });
                    tbLoad.updateSearchQuery();
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
                    tbLoad.searchQueryParams = _.merge(
                        tbLoad.searchQueryParams,
                        {
                            shipper: e.params.data.id,
                        });
                    tbLoad.updateSearchQuery();
                }).on('select2:unselect', () => {
                    tbLoad.searchQueryParams.shipper = null;
                    tbLoad.updateSearchQuery();
                });
            })();

            function downloadDispatch(){
                    var query = {
                        dateRange: dateRange.value,
                        shipper: $("#shipper").val(),
                    }

                    window.location = "{{url("load/DownloadExcelReport")}}?" + $.param(query);
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

            function openPicReport() {
                var query = {
                    dateRange: dateRange.value,
                    shipper: $("#shipper").val(),
                }

                window.location = "{{url("load/pictureReport")}}?" + $.param(query);
            }
        </script>
    @endsection

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <fieldset class="form-group col-5">
                        {!! Form::label('dateRange', 'Select Dates', ['class' => 'col-form-label']) !!}
                        {!! Form::text('dateRange', null, ['class' => 'form-control']) !!}
                    </fieldset>

                    <fieldset class="form-group col-6">
                        {!! Form::label('shipper', 'Shipper', ['class' => 'col-form-label']) !!}
                        {!! Form::select('shipper', [], null, ['class' => 'form-control']) !!}
                    </fieldset>

                    <fieldset class="form-group col-1">
                        <div class="dropdown float-right">
                            <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                <a class="dropdown-item" id="completeAll" onclick="downloadDispatch()"><i class="fas fa-file-excel"></i> Download Dispatch Report</a>
                                <a class="dropdown-item" id="openPicReport" onclick="openPicReport()" _blank><i class="fas fa-file-image"></i> Picture Report</a>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
