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
                    {!! Form::label('shipper_id', ucfirst(__('shipper')), ['class' => 'col-form-label']) !!}
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
                @endif
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('origin', ucfirst(__('origin')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('origin', $trip->origin ?? null, ['class' => 'form-control' . ($errors->first('origin') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addOrigin"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                        @error('origin')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! Form::hidden('origin_coords', $trip->origin_coords ?? null, ['class' => 'form-control' . ($errors->first('origin_coords') ? ' is-invalid' : '')]) !!}
                    @error('origin_coords')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('destination', ucfirst(__('destination')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('destination', $trip->destination ?? null, ['class' => 'form-control' . ($errors->first('destination') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addDestination"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                        @error('destination')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! Form::hidden('destination_coords', $trip->destination_coords ?? null, ['class' => 'form-control' . ($errors->first('destination_coords') ? ' is-invalid' : '')]) !!}
                    @error('destination_coords')
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
