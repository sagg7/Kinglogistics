<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group @if(auth()->guard('web')->check()){{ 'col-md-3' }}@else{{ 'col-md-4' }}@endif">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $trip->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group @if(auth()->guard('web')->check()){{ 'col-md-3' }}@else{{ 'col-md-4' }}@endif">
                    {!! Form::label('customer_name', ucfirst(__('customer name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_name', $trip->customer_name ?? null, ['class' => 'form-control' . ($errors->first('customer_name') ? ' is-invalid' : '')]) !!}
                    @error('customer_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group @if(auth()->guard('web')->check()){{ 'col-md-3' }}@else{{ 'col-md-4' }}@endif">
                    {!! Form::label('zone_id', ucfirst(__('zone')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('zone_id', $zones, $trip->zone_id ?? null, ['class' => 'form-control' . ($errors->first('zone_id') ? ' is-invalid' : '')]) !!}
                    @error('zone_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @if(auth()->guard('web')->check())
                <div class="form-group col-md-3">
                    {!! Form::label('shipper_id', ucfirst(__('customer')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('shipper_id', [], $trip->shipper_id ?? null, ['class' => 'form-control' . ($errors->first('shipper_id') ? ' is-invalid' : '')]) !!}
                    @error('shipper_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('rate_id', ucfirst(__('rate')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('rate_id', [], $trip->shipper_id ?? null, ['class' => 'form-control' . ($errors->first('rate_id') ? ' is-invalid' : ''), 'disabled']) !!}
                    @error('rate_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('status', ucfirst(__('status')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('status', $statuses, $trip->status ?? null, ['class' => 'form-control' . ($errors->first('status') ? ' is-invalid' : '')]) !!}
                    @error('status')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('status_current', ucfirst(__('status current')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('status_current', $trip->status_current ?? null, ['class' => 'form-control' . ($errors->first('status_current') ? ' is-invalid' : '')]) !!}
                    @error('status_current')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('status_total', ucfirst(__('status total')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('status_total', $trip->status_total ?? null, ['class' => 'form-control' . ($errors->first('status_total') ? ' is-invalid' : '')]) !!}
                    @error('status_total')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @endif
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('origin_id', ucfirst(__('origin')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('origin_id', isset($trip->origin_id) ? [$trip->origin_id => $trip->trip_origin->name] : [], $trip->origin_id ?? null, ['class' => 'form-control' . ($errors->first('origin_id') ? ' is-invalid' : '')]) !!}
                    @error('origin_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('destination_id', ucfirst(__('destination')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('destination_id', isset($trip->destination_id) ? [$trip->destination_id => $trip->trip_destination->name] : [], $trip->destination_id ?? null, ['class' => 'form-control' . ($errors->first('destination_id') ? ' is-invalid' : '')]) !!}
                    @error('destination_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('mileage', ucfirst(__('mileage')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('mileage', $trip->mileage ?? null, ['class' => 'form-control' . ($errors->first('mileage') ? ' is-invalid' : '')]) !!}
                    @error('mileage')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
