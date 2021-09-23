<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "view-photo", "title" => "Photo"])
    @endsection
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.0') }}"></script>
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/laravel-echo/echo.js') }}"></script>
        <script defer>
            var tbActive = null,
                tbFinished = null;
            (() => {
                class FrontDataSource {
                    constructor(data) {
                        this.load = data.load;
                    }
                    getRows(params) {
                        const current = tbActive.dataSource.data;
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
                tbActive = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                        {headerName: 'Photos', field: 'load_status', filter: false, cellRenderer: PhotosRenderer},
                        {headerName: 'Control #', field: 'control_number', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'Customer Reference', field: 'customer_reference', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'BOL', field: 'bol', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'Status', field: 'status', valueFormatter: capitalizeStatus},
                    ],
                    gridOptions: {
                        PhotosRenderer: PhotosRenderer,
                        undoRedoCellEditing: true,
                        onCellEditingStopped: function (event) {
                            if (event.value === '' || typeof event.value === "undefined") {
                                tbActive.gridOptions.api.undoCellEditing();
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
                                    tbActive.gridOptions.api.undoCellEditing();
                                    throwErrorMsg();
                                }
                            });
                        },
                    },
                    container: 'myGrid',
                    url: '/load/search',
                    tableRef: 'tbActive',
                });

                $('#view-photo').on('show.bs.modal', function(e) {
                    const modal = $(e.currentTarget),
                        modalBody = modal.find('.modal-body'),
                        content = modal.find('.content-body'),
                        anchor = $(e.relatedTarget),
                        modalSpinner = modalBody.find('.modal-spinner'),
                        img = anchor.find('img');
                        content.html(`<form id="replace-form" action="{{ url('load/replacePhoto') }}/${img.attr('customid')}" method="POST"">
                                <div class="row">
                                <div class="file-group col-md-9">
                                <label for="replacement"
                                       class="btn form-control btn-warning btn-block">
                                    <i class="fas fa-file"></i><span class="file-name">Replace File</span>
                                    <input type="file" name="replacement" id="replacement" accept="image/*" hidden>
                                </label>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                                </div>
                                </div>
                                <div class="input-group-append col-md-3">
                                    <button type="submit" class="btn btn-block btn-success">submit</button>
                                </div>
                                </form>
                            </div>
                        <img src="${img.attr('src')}" alt="photo" class="img-fluid">`);
                    modalSpinner.addClass('d-none');
                    content.removeClass('d-none');
                    initUpload();
                });
                window.Echo.private('load-status-update')
                    .listen('LoadUpdate', res => {
                        if (tbActive) {
                            const find = tbActive.dataSource.data.rows.find(obj => Number(obj.id) === Number(res.load.id));
                            if (find) {
                                const frontData = new FrontDataSource({load: res.load});
                                tbActive.gridOptions.api.setServerSideDatasource(frontData);
                            }
                        }
                    });
            })();

            function initUpload() {

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
                    console.log("eee");
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
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
