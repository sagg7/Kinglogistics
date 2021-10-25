<x-app-layout>
    <x-slot name="crumb_section">Diesel Charges</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script>
            (() => {
                const dateInp = $('.pickadate-months-year');
                $.each(dateInp, (i, item) => {
                    const inp = $(item);
                    const date = initPickadate(inp).pickadate('picker');
                    date.set('select', inp.val(), {format: 'yyyy/mm/dd'});
                });

                const uploadModal = $('#uploadExcel');
                $('#uploadDiesel').submit((e) => {
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
                    <div class="modal-body">
                        {!! Form::open(['route' => ['charge.uploadDieselExcel'], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data', 'id' => 'uploadDiesel']) !!}
                        <div class="file-group" style="margin-bottom: 20px">
                            <label for="fileExcel" class="btn form-control btn-block"  style="height: 200px; width: 200px; border-radius: 50%; margin: auto; background-color: #1c7430">
                                <i class="fas fa-file-upload fa-5x" style="color: white; margin-top: 50px"></i>
                                <input type="file" name="fileExcel" id="fileExcel" hidden>
                            </label>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-block mr-1 mb-1 waves-effect waves-light submit-ajax" >Upload</button>
                        {!! Form::close() !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    {!! Form::open(['route' => 'charge.storeDiesel', 'method' => 'post', 'class' => 'form form-vertical']) !!}
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
                                <a class="dropdown-item" id="uploadDiesel" data-toggle="modal" data-target="#uploadExcel"><i class="fas fa-file-upload"></i> Upload Diesel Template</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table">
                        <colgroup>
                            <col style="width: 40%;">
                            <col style="width: 20%;">
                            <col style="width: 20%;">
                            <col style="width: 20%;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Carrier</th>
                            <th>Gallons</th>
                            <th>Diesel Price</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbdoy>
                            @foreach($carriers as $id => $carrier)
                                <tr>
                                    <td>{{ $carrier }}</td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-gas-pump"></i></span>
                                            </div>
                                            {!! Form::text("gallons[$id]",null,['class' => 'form-control']) !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon2"><i class="fas fa-dollar-sign"></i></span>
                                            </div>
                                            {!! Form::text("diesel[$id]",null,['class' => 'form-control']) !!}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon3"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            {!! Form::text("date[$id]",null,['class' => 'form-control pickadate-months-year']) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbdoy>
                    </table>
                </div>
            </div>
            {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
</x-app-layout>
