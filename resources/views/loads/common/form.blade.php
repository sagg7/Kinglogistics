<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('load_type_id', ucfirst(__('load type')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::select('load_type_id', $load_types, $load->load_type_id ?? null, ['class' => 'form-control' . ($errors->first('load_type_id') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" data-toggle="modal" data-target="#addTrailerType"><i class="fas fa-plus"></i></button>
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
                    {!! Form::text('origin', $load->origin ?? null, ['class' => 'form-control' . ($errors->first('origin') ? ' is-invalid' : '')]) !!}
                    @error('origin')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('destination', ucfirst(__('destination')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('destination', $load->destination ?? null, ['class' => 'form-control' . ($errors->first('destination') ? ' is-invalid' : '')]) !!}
                    @error('destination')
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
                    {!! Form::label('sand_type', ucfirst(__('sand type')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('sand_type', $load->sand_type ?? null, ['class' => 'form-control' . ($errors->first('sand_type') ? ' is-invalid' : '')]) !!}
                    @error('sand_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
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
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div>
