<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $shipper->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('email', ucfirst(__('email')), ['class' => 'col-form-label']) !!}
                    {!! Form::email('email', $shipper->email ?? null, ['class' => 'form-control' . ($errors->first('email') ? ' is-invalid' : '')]) !!}
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('password', ucfirst(__('password')), ['class' => 'col-form-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control' . ($errors->first('password') ? ' is-invalid' : '')]) !!}
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('password_confirmation', ucfirst(__('password confirmation')), ['class' => 'col-form-label']) !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control' . ($errors->first('password_confirmation') ? ' is-invalid' : '')]) !!}
                    @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('phone', ucfirst(__('phone')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('phone', $shipper->phone ?? null, ['class' => 'form-control' . ($errors->first('invoice_email') ? ' is-invalid' : '')]) !!}
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('invoice_email', ucfirst(__('invoice email')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('invoice_email', $shipper->invoice_email ?? null, ['class' => 'form-control' . ($errors->first('invoice_email') ? ' is-invalid' : ''), 'data-email' => 'multi']) !!}
                    @error('invoice_email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @if(auth()->guard('web')->check())
                <div class="form-group col-md-6">
                    {!! Form::label('payment_days', ucfirst(__('payment days')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('payment_days[]', $weekdays, $shipper->payment_days ?? null, ['class' => 'form-control select2' . ($errors->first('payment_days') ? ' is-invalid' : ''), 'multiple']) !!}
                    @error('payment_days')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @endif
                <div class="form-group col-md-6">
                    {!! Form::label('trucks_required', ucfirst(__('trucks required')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('trucks_required', $shipper->trucks_required ?? null, ['class' => 'form-control' . ($errors->first('trucks_required') ? ' is-invalid' : '')]) !!}
                    @error('trucks_required')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('loads_per_invoice', ucwords(__('maximum quantity of loads per invoice')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('loads_per_invoice', $shipper->loads_per_invoice ?? null, ['class' => 'form-control' . ($errors->first('loads_per_invoice') ? ' is-invalid' : '')]) !!}
                    @error('loads_per_invoice')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('type_rate', ucfirst(__('type rate')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('type_rate', ['mileage' =>'mileage', 'mileage-tons'=> 'mileage-tons'], $shipper->type_rate ?? null, ['class' => 'form-control select2' . ($errors->first('type_rate') ? ' is-invalid' : '')]) !!}
                    @error('type_rate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <div class="row">
                        <fieldset class="col-6">
                            {!! Form::label('factoring', ucfirst(__('Approved for factoring')), ['class' => 'col-form-label']) !!}
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                {{ Form::checkbox('factoring', 1, $shipper->factoring ?? null) }}
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                            </div>
                        </fieldset>
                        <div class="col-6">
                            {!! Form::label('days_to_pay', ucfirst(__('Days to pay')), ['class' => 'col-form-label']) !!}
                            <i class="fas fa-info-circle" data-toggle="popover" data-trigger="hover" data-content="Number of days that must elapse to proceed with the load payment "></i>
                            {!! Form::text('days_to_pay', $shipper->days_to_pay ?? null, ['class' => 'form-control' . ($errors->first('trucks_required') ? ' is-invalid' : '')]) !!}
                            @error('days_to_pay')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div>
