<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('rate_group', ucfirst(__('rate group')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::select('rate_group', $rate_groups, $rate->rate_group_id ?? null, ['class' => 'form-control' . ($errors->first('rate_group') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addRateGroup"><i class="fas fa-plus"></i></button>
                        </div>
                        @error('rate_group')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('shipper', ucfirst(__('customer')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('shipper', [], $rate->shipper_id ?? null, ['class' => 'form-control' . ($errors->first('shipper') ? ' is-invalid' : '')]) !!}
                    @error('shipper')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('zone', ucfirst(__('zone')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('zone', [], $rate->zone_id ?? null, ['class' => 'form-control' . ($errors->first('zone') ? ' is-invalid' : '')]) !!}
                    @error('zone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-3">
                    <fieldset>
                        {!! Form::label('start_mileage', ucfirst(__('start mileage')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-tachometer-alt"></i></span>
                            </div>
                            {!! Form::text('start_mileage', $rate->start_mileage ?? null, ['class' => 'form-control' . ($errors->first('start_mileage') ? ' is-invalid' : '')]) !!}
                            @error('start_mileage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-3">
                    <fieldset>
                        {!! Form::label('end_mileage', ucfirst(__('end mileage')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-tachometer-alt"></i></span>
                            </div>
                            {!! Form::text('end_mileage', $rate->end_mileage ?? null, ['class' => 'form-control' . ($errors->first('end_mileage') ? ' is-invalid' : '')]) !!}
                            @error('end_mileage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-3">
                    <fieldset>
                        {!! Form::label('shipper_rate', ucfirst(__('customer rate')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            {!! Form::text('shipper_rate', $rate->shipper_rate ?? null, ['class' => 'form-control' . ($errors->first('shipper_rate') ? ' is-invalid' : '')]) !!}
                            @error('shipper_rate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-3">
                    <fieldset>
                        {!! Form::label('carrier_rate', ucfirst(__('carrier rate')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            {!! Form::text('carrier_rate', $rate->carrier_rate ?? null, ['class' => 'form-control' . ($errors->first('carrier_rate') ? ' is-invalid' : '')]) !!}
                            @error('carrier_rate')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
