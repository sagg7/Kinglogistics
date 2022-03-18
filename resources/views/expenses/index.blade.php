<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">View</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            let tbAG = null;
            (() => {
                const nameFormatter = (params) => {
                    if (params.value)
                        return params.value.name;
                    else
                        return '';
                };
                const moneyFormatter = (params) => {
                    if (params.value)
                        return numeral(params.value).format('$0,0.00');
                    else
                        return '$0.00';
                };
                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Date', field: 'date'},
                        {headerName: 'Type', field: 'type', valueFormatter: nameFormatter},
                        {headerName: 'Account', field: 'account', valueFormatter: nameFormatter},
                        {headerName: 'Amount', field: 'amount', valueFormatter: moneyFormatter},
                    ],
                    menu: [
                        @if(auth()->user()->can(['update-expense']))
                        {text: 'Edit', route: '/expense/edit', icon: 'feather icon-edit'},
                        @endif
                        @if(auth()->user()->can(['delete-expense']))
                        {route: '/expense/delete', type: 'delete'}
                        @endif
                    ],
                    container: 'myGrid',
                    url: '/expense/search',
                    tableRef: 'tbAG',
                });

                const uploadModal = $('#uploadExcel');
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

                $('#uploadExpense').submit((e) => {
                    e.preventDefault();
                    const form = $(e.currentTarget),
                        url = form.attr('action');
                    let formData = new FormData(form[0]);
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
        </script>
    @endsection
    @section('modals')
    <div class="modal fade" id="uploadExcel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="dropdown float-right">
                    <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                        <a class="dropdown-item" id="downloadTmpXLS" href="/expense/downloadTmpXLS"><i class="fas fa-file-excel"></i> Download Excel Template</a>
                    </div>
                </div>
                <BR>
                <div class="modal-body">
                    {!! Form::open(['route' => ['expense.uploadExpenseExcel'], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data', 'id' => 'uploadExpense']) !!}
                    <div class="file-group" style="margin-bottom: 20px">
                        <label for="fileExcel" class="btn form-control btn-block bg-warning"  style="height: 200px; width: 200px; border-radius: 50%; margin: auto;">
                            <i class="fas fa-file-upload fa-5x" style="color: white; margin-top: 50px"></i>
                            <input type="file" name="fileExcel" id="fileExcel" hidden accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </label>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-block mr-1 mb-1 waves-effect waves-light submit-ajax text-white" disabled>Upload</button>
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

    @component('components.aggrid-index', auth()->user()->can(['create-expense']) ? ['create_btn' => ['url' => '/expense/create', 'text' => 'Create Expense'], 'menu' => [['url'=>'/expense/downloadXLS','icon'=>'fas fa-file-excel', 'text' => 'Download Expense'],['url'=>'#uploadExcel','icon'=>'fas fa-file-upload', 'text' => 'Upload Expenses Template','attributes' => ['data-toggle'=>'modal', 'data-target'=>'#uploadExcel','id'=>'uploadExpense']]]] : [])@endcomponent

</x-app-layout>
