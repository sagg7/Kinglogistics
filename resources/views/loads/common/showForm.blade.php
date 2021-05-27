<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('driver', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('driver', $load->driver->name ?? null, ['class' => 'form-control' . ($errors->first('driver') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('driver')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @if(auth()->guard('web')->check())
                    <div class="form-group col-md-3">
                        {!! Form::label('shipper_id', ucfirst(__('shipper')), ['class' => 'col-form-label']) !!}
                        {!! Form::text('shipper_id', $load->shipper->name ?? null, ['class' => 'form-control' . ($errors->first('shipper_id') ? ' is-invalid' : ''), 'readonly']) !!}
                        @error('shipper_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                @endif
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('load_type', ucfirst(__('load type')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('load_type', $load->load_type->name ?? null, ['class' => 'form-control' . ($errors->first('load_type') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('load_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('date_read', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('date_read', $load->date->toFormattedDateString() ?? null, ['class' => 'form-control' . ($errors->first('date') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('date_read')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('origin', ucfirst(__('origin')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('origin', $load->origin ?? null, ['class' => 'form-control' . ($errors->first('origin') ? ' is-invalid' : ''), 'readonly']) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addOrigin"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                        @error('origin')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! Form::hidden('origin_coords', $load->origin_coords ?? null, ['class' => 'form-control' . ($errors->first('origin_coords') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('origin_coords')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('destination', ucfirst(__('destination')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('destination', $load->destination ?? null, ['class' => 'form-control' . ($errors->first('destination') ? ' is-invalid' : ''), 'readonly']) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addDestination"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                        @error('destination')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! Form::hidden('destination_coords', $load->destination_coords ?? null, ['class' => 'form-control' . ($errors->first('destination_coords') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('destination_coords')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('control_number', ucfirst(__('control number')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('control_number', $load->control_number ?? null, ['class' => 'form-control' . ($errors->first('control_number') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('control_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('customer_name', ucfirst(__('customer name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_name', $load->customer_name ?? null, ['class' => 'form-control' . ($errors->first('customer_name') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('customer_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('customer_po', ucfirst(__('customer po')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_po', $load->customer_po ?? null, ['class' => 'form-control' . ($errors->first('customer_po') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('customer_po')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('customer_reference', ucfirst(__('customer reference')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_reference', $load->customer_reference ?? null, ['class' => 'form-control' . ($errors->first('customer_reference') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('customer_reference')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('weight', ucfirst(__('weight')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('weight', $load->weight ?? null, ['class' => 'form-control' . ($errors->first('weight') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('weight')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('tons', ucfirst(__('tons')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('tons', $load->tons ?? null, ['class' => 'form-control' . ($errors->first('tons') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('tons')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('silo_number', ucfirst(__('silo number')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('silo_number', $load->silo_number ?? null, ['class' => 'form-control' . ($errors->first('silo_number') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('silo_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('mileage', ucfirst(__('mileage')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('mileage', $load->mileage ?? null, ['class' => 'form-control' . ($errors->first('mileage') ? ' is-invalid' : ''), 'readonly']) !!}
                    @error('mileage')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
    </div> <!-- end card-body -->
</div>
