<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>


    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.1') }}"></script>
        <script defer>
            var tbEquipment = null;
            (() => {
                const capitalizeNameFormatter = (params) => {
                    if (params.value)
                        return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
                    else
                        return '';
                };

                $('.nav-pills .nav-link').click((e) => {
                    const link = $(e.currentTarget),
                        href = link.attr('href');
                    switch (href) {
                        case '#pane-equipment':
                            if (!tbEquipment)
                                tbEquipment = new tableAG({
                                    columns: [
                                        {headerName: 'Name', field: 'name'},
                                        {headerName: 'Status', field: 'status', valueFormatter: capitalizeNameFormatter},
                                        {headerName: 'Description', field: 'description'},
                                    ],
                                    container: 'equipmentGrid',
                                    url: '/carrier/equipment/search',
                                    tableRef: 'tbAG',
                                    successCallback: (params) => {
                                        tbEquipment.gridOptions.columnApi.autoSizeAllColumns();
                                        tbEquipment.gridOptions.api.sizeColumnsToFit();
                                    }
                                });
                            break;
                    }
                });
            })();
        </script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [
    ['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],
    ['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork'],
    ['name' => 'Equipment', 'icon' => 'fas fa-toolbox', 'pane' => 'pane-equipment'],
    ]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general"
             aria-expanded="true">
            {!! Form::open(['route' => ['carrier.update', $carrier->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('carriers.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            @include('common.paperwork.filesTemplates', ['related_id' => $carrier->id])
            <hr>
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('common.paperwork.filesUploads', ['related_id' => $carrier->id, 'type' => 'carrier'])
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-equipment" aria-labelledby="pane-equipment">
            <div id="equipmentGrid"></div>
        </div>
    @endcomponent

</x-app-layout>
