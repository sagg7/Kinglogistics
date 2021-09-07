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
                let getRole = (params) => {
                    if (params.data)
                        return params.data.roles[0].name;
                };
                function PhoneCall() {}
                PhoneCall.prototype.init = (params) => {
                    this.eGui = document.createElement('div');
                    if (params.value) {
                            html = `<a href="tel:${params.value}">${params.value}</a>`;
                        this.eGui.innerHTML = html;
                    }
                }
                PhoneCall.prototype.getGui = () => {
                    return this.eGui;
                }
                function mailTo() {}
                mailTo.prototype.init = (params) => {
                    this.eGui = document.createElement('div');
                    if (params.value) {
                            html = `<a href="mailto:${params.value}">${params.value}</a>`;
                        this.eGui.innerHTML = html;
                    }
                }
                mailTo.prototype.getGui = () => {
                    return this.eGui;
                }
                tbAG = new tableAG({
                    columns: [
                        //{headerName: 'Fecha', field: 'date'},
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Role', field: 'role', valueFormatter: getRole},
                        {headerName: 'Email', field: 'email', cellRenderer: mailTo},
                        {headerName: 'Phone', field: 'phone', cellRenderer: PhoneCall},
                    ],
                    gridOptions: {
                        PhoneCall: PhoneCall,
                        mailTo: mailTo,
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
                    container: 'myGrid',
                    url: '/staff/searchActive',
                    tableRef: 'tbAG',
                    successCallback: (res) => {
                        res.rows.forEach((item) => {
                            item.area = item.roles.name;
                        });
                    }
                });
            })();
        </script>
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
