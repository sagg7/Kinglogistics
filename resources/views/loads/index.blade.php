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
        <script defer>
            var tbAG = null;
            const role = "{{ auth()->user()->getRole() }}";
            (() => {
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                let columns = [],
                    menu = [];
                switch (role) {
                    case 'dispatch':
                        columns = [
                            {headerName: 'Date', field: 'date'},
                            {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                            {headerName: 'Photos', field: 'photos', cellRenderer: PhotosRenderer},
                            {headerName: 'Control #', field: 'control_number', editable: true},
                            {headerName: 'Sand ticket', field: 'sand_ticket', editable: true},
                            {headerName: 'BOL', field: 'bol', editable: true},
                        ];
                        break;
                    case 'admin':
                        columns = [
                            {headerName: 'Date', field: 'date'},
                            {headerName: 'Driver', field: 'driver', valueFormatter: nameFormatter},
                            {headerName: 'Control #', field: 'control_number'},
                            {headerName: 'Origin', field: 'origin'},
                            {headerName: 'Destination', field: 'destination'},
                        ];
                        menu = [
                            {text: 'Show', route: '/load/show', icon: 'feather icon-eye'},
                                @if(auth()->guard('web')->check())
                            {text: 'Edit', route: '/load/edit', icon: 'feather icon-edit'},
                            {route: '/load/delete', type: 'delete'}
                            @endif
                        ];
                        break;
                }
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
                tbAG = new tableAG({
                    columns,
                    menu,
                    gridOptions: {
                        PhotosRenderer: PhotosRenderer,
                        undoRedoCellEditing: true,
                        onCellEditingStopped: function (event) {
                            if (event.value === '') {
                                tbAG.gridOptions.api.undoCellEditing();
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
                                    tbAG.gridOptions.api.undoCellEditing();
                                    throwErrorMsg();
                                }
                            });
                        },
                    },
                    container: 'myGrid',
                    url: '/load/search',
                    tableRef: 'tbAG',
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
