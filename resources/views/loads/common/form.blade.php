<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                @if(!isset($load))
                    <div class="form-group col-md-3">
                        {!! Form::label('load_number', ucfirst(__('Number of loads')), ['class' => 'col-form-label']) !!}
                        {!! Form::text('load_number', $load->load_number ?? null, ['class' => 'form-control' . ($errors->first('load_number') ? ' is-invalid' : '')]) !!}
                        @error('load_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                @else
                    <div class="form-group col-md-3">
                        {!! Form::label('driver', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                        {!! Form::text('driver', $load->driver->name ?? null, ['class' => 'form-control' . ($errors->first('driver') ? ' is-invalid' : ''), 'readonly']) !!}
                        @error('driver')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                @endif
                @if(auth()->guard('web')->check())
                <div class="form-group col-md-3">
                    {!! Form::label('shipper_id', ucfirst(__('shipper')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('shipper_id', $shippers, $load->shipper_id ?? null, ['class' => 'form-control' . ($errors->first('shipper_id') ? ' is-invalid' : '')]) !!}
                    @error('shipper_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('trip_id', ucfirst(__('trip')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('trip_id', [], $load->trip_id ?? null, ['class' => 'form-control' . ($errors->first('trip_id') ? ' is-invalid' : '')]) !!}
                    @error('trip_id')
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
                    {!! Form::label('load_type_id', ucfirst(__('load type')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::select('load_type_id', [], $load->load_type_id ?? null, ['class' => 'form-control' . ($errors->first('load_type_id') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addLoadType"><i class="fas fa-plus"></i></button>
                        </div>
                        @error('load_type_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <!--<div class="form-group col-md-3">
                    {!! Form::label('driver_id', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('driver_id', [], $load->driver_id ?? null, ['class' => 'form-control' . ($errors->first('driver_id') ? ' is-invalid' : '')]) !!}
                    @error('driver_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>-->
                <div class="form-group col-md-3">
                    {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('date', $load->date ?? null, ['class' => 'form-control' . ($errors->first('date') ? ' is-invalid' : '')]) !!}
                    @error('date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('origin', ucfirst(__('origin')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('origin', $load->origin ?? null, ['class' => 'form-control' . ($errors->first('origin') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addOrigin"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                        @error('origin')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! Form::hidden('origin_coords', $load->origin_coords ?? null, ['class' => 'form-control' . ($errors->first('origin_coords') ? ' is-invalid' : '')]) !!}
                    @error('origin_coords')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('destination', ucfirst(__('destination')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('destination', $load->destination ?? null, ['class' => 'form-control' . ($errors->first('destination') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addDestination"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                        @error('destination')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    {!! Form::hidden('destination_coords', $load->destination_coords ?? null, ['class' => 'form-control' . ($errors->first('destination_coords') ? ' is-invalid' : '')]) !!}
                    @error('destination_coords')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('control_number', ucfirst(__('control number')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('control_number', $load->control_number ?? null, ['class' => 'form-control' . ($errors->first('control_number') ? ' is-invalid' : '')]) !!}
                    @error('control_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('customer_name', ucfirst(__('customer name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_name', $load->customer_name ?? null, ['class' => 'form-control' . ($errors->first('customer_name') ? ' is-invalid' : '')]) !!}
                    @error('customer_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('customer_po', ucfirst(__('customer po')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_po', $load->customer_po ?? null, ['class' => 'form-control' . ($errors->first('customer_po') ? ' is-invalid' : '')]) !!}
                    @error('customer_po')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('customer_reference', ucfirst(__('customer reference')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_reference', $load->customer_reference ?? null, ['class' => 'form-control' . ($errors->first('customer_reference') ? ' is-invalid' : '')]) !!}
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
                    {!! Form::text('weight', $load->weight ?? null, ['class' => 'form-control' . ($errors->first('weight') ? ' is-invalid' : '')]) !!}
                    @error('weight')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('tons', ucfirst(__('tons')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('tons', $load->tons ?? null, ['class' => 'form-control' . ($errors->first('tons') ? ' is-invalid' : '')]) !!}
                    @error('tons')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('silo_number', ucfirst(__('silo number')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('silo_number', $load->silo_number ?? null, ['class' => 'form-control' . ($errors->first('silo_number') ? ' is-invalid' : '')]) !!}
                    @error('silo_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('mileage', ucfirst(__('mileage')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('mileage', $load->mileage ?? null, ['class' => 'form-control' . ($errors->first('mileage') ? ' is-invalid' : '')]) !!}
                    @error('mileage')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="form-group col-md-12">
                {!! Form::label('notes', ucfirst(__('notes')), ['class' => 'col-form-label']) !!}
                {!! Form::textarea('notes', $load->notes ?? null, ['class' => 'form-control' . ($errors->first('notes') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 512]) !!}
                @error('notes')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ ucfirst($message) }}</strong>
                </span>
                @enderror
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div>
