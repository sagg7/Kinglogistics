<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('trailer_type_id', ucfirst(__('trailer type')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('trailer_type_id', $trailer_types, $trailer->trailer_type_id ?? null, ['class' => 'form-control' . ($errors->first('trailer_type_id') ? ' is-invalid' : '')]) !!}
                    @error('trailer_type_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('number', ucfirst(__('number')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('number', $trailer->number ?? null, ['class' => 'form-control' . ($errors->first('number') ? ' is-invalid' : '')]) !!}
                    @error('number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('plate', ucfirst(__('plate')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('plate', $trailer->plate ?? null, ['class' => 'form-control' . ($errors->first('plate') ? ' is-invalid' : '')]) !!}
                    @error('plate')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('vin', ucfirst(__('vin')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('vin', $trailer->plate ?? null, ['class' => 'form-control' . ($errors->first('vin') ? ' is-invalid' : '')]) !!}
                    @error('vin')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('status', ucfirst(__('status')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('status', $statuses, $trailer->status ?? null, ['class' => 'form-control' . ($errors->first('status') ? ' is-invalid' : '')]) !!}
                    @error('status')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <!--<div class="form-group col-md-6">
                    <fieldset>
                        {!! Form::label('inactive', ucfirst(__('inactive')), ['class' => 'col-form-label']) !!}
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            {{ Form::checkbox('inactive', 'inactive', $trailer->is_paid ?? null) }}
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                    </fieldset>
                </div>-->
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
