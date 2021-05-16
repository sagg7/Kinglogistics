<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('carrier_id', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('carrier_id', [], $rental->carrier_id ?? null, ['class' => 'form-control' . ($errors->first('carrier_id') ? ' is-invalid' : '')]) !!}
                    @error('carrier_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('driver_id', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('driver_id', [], $rental->driver_id ?? null, ['class' => 'form-control' . ($errors->first('driver_id') ? ' is-invalid' : '')]) !!}
                    @error('driver_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('trailer_id', ucfirst(__('trailer')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('trailer_id', [], $rental->trailer_id ?? null, ['class' => 'form-control' . ($errors->first('trailer_id') ? ' is-invalid' : '')]) !!}
                    @error('trailer_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('date', $rental->date ?? null, ['class' => 'form-control' . ($errors->first('date') ? ' is-invalid' : '')]) !!}
                    @error('date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('period', ucfirst(__('period')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('period', $periods, $rental->period ?? null, ['class' => 'form-control select2' . ($errors->first('period') ? ' is-invalid' : '')]) !!}
                    @error('period')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <fieldset>
                        {!! Form::label('cost', ucfirst(__('cost')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">$</span>
                            </div>
                            {!! Form::text('cost', $rental->cost ?? null, ['class' => 'form-control' . ($errors->first('cost') ? ' is-invalid' : '')]) !!}
                            @error('cost')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-4">
                    <div class="row">
                        <div class="col-xl-10 col-9">
                            <fieldset style="width: 90%;">
                                {!! Form::label('deposit', ucfirst(__('deposit')), ['class' => 'col-form-label']) !!}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">$</span>
                                    </div>
                                    {!! Form::text('deposit', $rental->deposit ?? null, ['class' => 'form-control' . ($errors->first('deposit') ? ' is-invalid' : '')]) !!}
                                    @error('deposit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </fieldset>
                        </div>
                        <div class="col pl-0 text-center">
                            <fieldset>
                                {!! Form::label('is_paid', ucfirst(__('is paid')), ['class' => 'col-form-label']) !!}
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    {{ Form::checkbox('is_paid', 1, $rental->deposit_is_paid ?? null) }}
                                    <span class="vs-checkbox mx-auto">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
