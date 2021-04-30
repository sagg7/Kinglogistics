@extends('layouts.app')

@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--6" bis_skin_checked="1">
        <div class="row justify-content-center" bis_skin_checked="1">
            <div class=" col " bis_skin_checked="1">
                <div class="card" id="vehicleInspection" bis_skin_checked="1">
                    <div class="card-header bg-transparent" bis_skin_checked="1">
                        <h3 class="mb-0">Rent Trailer</h3>
                    </div>
                    {!! Form::open(['route' => 'rental.store', 'method' => 'POST', 'role' => 'form','id'=>'rentalForm','name'=>'rentalForm']) !!}
                    <div class="card-body" bis_skin_checked="1">
                        <div><h2>{{$leased->name}}</h2></div>
                        <input type="hidden" name="leased_id" value="{{$leased->id}}">
                        <div class="row">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('trailer_id', 'Trailer') !!}
                                    {!! Form::select('trailer_id', $trailers, $rental->trailer_id ?? null, ['class' => 'input-os k-select']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('driver_id', 'Driver') !!}
                                    {!! Form::select('driver_id', $drivers, $rental->driver_id ?? null, ['class' => 'input-os k-select']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('pickup_location', 'Pickup location') !!}
                                    {!! Form::text('pickup_location', $rental->pickup_location ?? null, ['class' => 'form-control', 'placeholder' => 'Pickup location']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('cost', 'Cost') !!}
                                    {!! Form::text('cost', $rental->trailer_id ?? null, ['class' => 'form-control', 'placeholder' => 'Cost']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('periodicity', 'Periodicity') !!}
                                    {!! Form::select('periodicity', ['weekly', 'monthly', 'annual'], $rental->periodicity ?? null, ['class' => 'input-os k-select']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('deposit', 'Deposit amount') !!}
                                    {!! Form::text('deposit_amount', $rental->deposit_amount ?? null, ['class' => 'form-control', 'placeholder' => 'Deposit amount']) !!}

                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                {!! Form::label('is_paid', 'Is paid?', ['class' => 'custom-checkbox']) !!}
                                <label class="container checkbox">
                                    {!! Form::checkbox('is_paid', 1 ,$rental->is_paid ?? null, ['class' => 'custom-checkbox']) !!}
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" id="save" class="btn btn-primary btn-lg btn-block">Save</button>
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
    @push('css')
        <link href="{{ asset('assets') }}/css/select2.min.css" rel="stylesheet">
        <link href="{{ asset('assets') }}/css/checkbox.css" rel="stylesheet">
    @endpush
    @push('js')
        <script src="{{ asset('assets') }}/js/select2.min.js"></script>
        <script src="{{ asset('assets') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script>
            $('form[name=rentalForm]').submit(async function (e)
            {
                e.preventDefault();

                var formAction = $(this).attr('action');
                var formData = new FormData(document.getElementById('rentalForm'));
                $.ajax({
                    type        : 'POST',//    Define the type of HTTP verb we want to use (POST for our form).
                    url         : formAction,//   The url where we want to POST.
                    data        : formData,//  Our data object
                    processData: false,
                    contentType: false,
                    dataType    : 'json'// What type of data do we expect back from the server
                    //,encode    : true
                }).done(function(data) {
                    $.confirm({
                        animation: 'scale',
                        closeAnimation: 'scale',
                        animateFromElement: false,
                        columnClass: 'col-md-6 col-md-offset-3 span6 offset3',
                        type: 'blue',
                        title: "Success!",
                        content: `Rental created correctly,Do you want to carry out the visual inspection now?`,
                        backgroundDismiss: false,
                        buttons: {
                            confirm: {
                                text: 'Yes',
                                btnClass: 'btn-blue',
                                action: () => {
                                    window.location = `{{url('inspection/create')}}/${data.id}`;
                                }
                            },
                            cancel: {
                                text: 'No',
                                action: () => {
                                    window.location = `{{url('rentals')}}`;
                                }
                            }
                        }
                    });
                });
            });
            (() => {
                $("#trailer_id").select2({
                    placeholder: "Select the trailer",
                    allowClear: true
                }).val(null).trigger('change');
                $("#driver_id").select2({
                    placeholder: "Select a Driver",
                    allowClear: true
                }).val(null).trigger('change');
                $("#periodicity").select2({
                    placeholder: "how often will pay?",
                    allowClear: true
                });
            })();


        </script>
    @endpush
@endsection
