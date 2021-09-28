<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.0') }}"></script>
        <script defer>
            var tbEquipment = null;
            (() => {
                tbEquipment = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Status', field: 'status'},
                    ],
                    menu: [
                        {text: 'Edit', route: '/equipment/edit', icon: 'feather icon-edit'},
                        {route: '/equipment/delete', type: 'delete'}
                    ],
                    container: 'equipmentGrid',
                    url: '/equipment/search',
                    tableRef: 'tbAG',
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
            {!! Form::open(['route' => ['carrier.profile.update', $carrier->id, 1], 'method' => 'post', 'class' => 'form form-vertical']) !!}
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
            <a href="/equipment/create" class="btn btn-primary">Create equipment</a>
            <hr>
            <div id="equipmentGrid"></div>
        </div>
    @endcomponent
</x-app-layout>
