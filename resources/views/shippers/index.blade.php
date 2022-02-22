<x-app-layout>
    <x-slot name="crumb_section">Customer</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
            (() => {
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'name'},
                        {headerName: 'Email', field: 'email'},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-customer']))
                        {text: 'Edit', route: '/shipper/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-customer']))
                        {route: '/shipper/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/shipper/search',
                    tableRef: 'tbAG',
                });

                $('#runInvoices').click(function (e) {
                    $("#run-invoices").modal("show");
                });
                $('#button-run').click(function (e) {
                    $.ajax({
                        url: '/invoice/runInvoices/',
                        type: 'POST',
                        data: {
                            date: $('[name=date]').val()
                        },
                        success: (res) => {
                            throwErrorMsg("The invoices are being created please wait a minute", {"title": "Success!", "type": "success", "redirect": "{{route('invoice.payments')}}"})
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    });
                    $("#run-invoices").modal("hide");
                });
            })();
        </script>
    @endsection
    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "run-invoices", "title" => "Run Invoices", "content" => Form::label('date', ucfirst(__('Select maximum load date')), ['class' => 'col-form-label']).
                        '<div style = "min-height: 400px">
                            <div class="input-group id="date-div">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                                </div>'.
                                Form::text("date", (date('N', time()) == 1) ? date('Y-m-d') : date('Y-m-d', strtotime('last Monday')) ?? null, ['class' => 'form-control pickadate-months-year']).
                        '</div><div id=loader></div></div>' ,
                        'footerButton' => '<button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" id="button-run">Submit</button>'])
    @endsection
    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div class="row">
                    <div class="col-6 offset-6">
                        <div class="dropdown float-right">
                            <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                <a class="dropdown-item" id="runInvoices"><i class="fas fa-file-invoice"></i> Run invoices</a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="myGrid"></div>
            </div>
        </div>
    </div>
</x-app-layout>
