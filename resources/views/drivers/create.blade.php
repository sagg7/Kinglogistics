 @extends('layouts.app')

@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--6" bis_skin_checked="1">
        <div class="row justify-content-center" bis_skin_checked="1">
            <div class=" col " bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header bg-transparent" bis_skin_checked="1">
                        <h3 class="mb-0">Create Trailer</h3>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="alert alert-warning hide" id="warning" role="alert"></div>
                        <div class="" bis_skin_checked="1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Trailer Number" id="trailer_number" name="trailer_number" class="form-control form-control-alternative" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-alternative" id="trailer_plate" name="trailer_plate" placeholder="license plates">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Registration expiration date" id="expiration_date" name="expiration_date" class="form-control form-control-alternative" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::select('trailer_type', $trailerTypes, null, ['class' => 'input-os k-select']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="save" class="btn btn-primary btn-lg btn-block">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.footers.auth')
    @push('js')
        <script>
            $(document).ready(() => {
                $("#trailer_type").select2({
                    placeholder: "Select the trailer",
                    allowClear: true
                });
            });
            $("#save").click(function (){
                let error = "", trailer_number = $("#trailer_number").val(),
                trailer_plate = $("#trailer_plate").val(),
                expiration_date = $("#expiration_date").val(),
                address = $("#address").val();
               if (trailer_number == "")
                   error += '<div><strong>Warning!</strong> the trailer number is required!</div>'
               if (trailer_plate == "")
                   error += '<div><strong>Warning!</strong> the email is required!</div>'
                if (expiration_date == "")
                    error += '<div><strong>Warning!</strong> the phone is required!</div>'

                if (error != ""){
                    $("#warning").html(error).removeClass('hide');
                } else {

                    let formData ={trailer_number, trailer_plate, expiration_date}
                    $.ajax({
                        type: 'POST',//    Define the type of HTTP verb we want to use (POST for our form).
                        url: {{ route('trailer.store') }},
                        data: formData,
                        success: function (response) {
                            $.confirm({
                                animation: 'scale',
                                closeAnimation: 'scale',
                                animateFromElement: false,
                                columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                                title: 'Trailer created correctly! \n create another one?',
                                content: 'Yes',
                                backgroundDismiss: false,
                                buttons: {
                                    confirm: {
                                        text: 'Yes',
                                        btnClass: 'btn btn-blue',
                                        action: function () {
                                            window.location.reload();
                                        }
                                    },
                                    cancel: {
                                        text: 'No',
                                        action: function () {
                                            window.location = ( {{ url('trailers') }} );
                                        }
                                    }
                                }
                            });
                        }
                    }).fail(function () {
                        $.alert({
                            animation: 'scale',
                            closeAnimation: 'scale',
                            animateFromElement: false,
                            columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                            type: 'red',
                            title: 'Woops',
                            content: 'An error occurred, try again, if the problem persists contact support.',
                            buttons: {
                                confirm: {
                                    text: 'OK'
                                }
                            }
                        });
                        return false;
                    });
                }
            });
        </script>
    @endpush
@endsection
