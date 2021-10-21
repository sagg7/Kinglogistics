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

                $('#uploadDiesel').submit((e) => {
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
                                //throwErrorMsg("Image replaced Correctly", {"title": "Success!", "type": "success", "redirect": "{{url('load/index')}}"})
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
            })();


        </script>
    @endsection
    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "uploadExcel", "title" => "Upload Excel"])
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
