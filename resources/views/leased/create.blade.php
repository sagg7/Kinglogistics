@extends('layouts.app')

@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--6" bis_skin_checked="1">
        <div class="row justify-content-center" bis_skin_checked="1">
            <div class=" col " bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header bg-transparent" bis_skin_checked="1">
                        <h3 class="mb-0">Create Leased</h3>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="alert alert-warning hide" id="warning" role="alert"></div>
                        <div class="" bis_skin_checked="1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Company Name" id="name" class="form-control form-control-alternative" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-alternative" id="email" placeholder="name@example.com">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Phone" id="phone" class="form-control form-control-alternative" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Address" id="address" class="form-control form-control-alternative" />
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
            $("#save").click(function (){
                let error = "", name = $("#name").val(),
                email = $("#email").val(),
                phone = $("#phone").val(),
                address = $("#address").val();
               if (name == "")
                   error += '<div><strong>Warning!</strong> the name is required!</div>'
               if (email == "")
                   error += '<div><strong>Warning!</strong> the email is required!</div>'
                if (phone == "")
                    error += '<div><strong>Warning!</strong> the phone is required!</div>'
                if (address == "")
                    error += '<div><strong>Warning!</strong> the address is required!</div>'

                if (error != ""){
                    $("#warning").html(error).removeClass('hide');
                }
                else {

                    let formData ={name, email, phone, address}
                    $.ajax({
                        type: 'POST',//    Define the type of HTTP verb we want to use (POST for our form).
                        url: {{ route('leased.store') }},
                        data: formData,
                        success: function (response) {
                            $.confirm({
                                animation: 'scale',
                                closeAnimation: 'scale',
                                animateFromElement: false,
                                columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                                title: 'Leased Created correctly! \n create another one?',
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
                                            window.location = ( {{ url('leased') }} );
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
