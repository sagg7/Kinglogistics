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
                        let html = '';
                        params.value.forEach(item => {
                            html += `<a class="avatar" href="#view-photo" data-toggle="modal" data-target="#view-photo"><img src="${item.url}" alt="photo" width="32" height="32"></a>`;
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
                        {headerName: 'Photos', field: 'photos', cellRenderer: PhotosRenderer},
                        {headerName: 'Control #', field: 'control_number', editable: true, valueFormatter: emptyFormatter},
                        {headerName: 'Sand ticket', field: 'sand_ticket', editable: true, valueFormatter: emptyFormatter},
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

                    content.html(`<img src="${img.attr('src')}" alt="photo" class="img-fluid">`);
                    modalSpinner.addClass('d-none');
                    content.removeClass('d-none');
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
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
