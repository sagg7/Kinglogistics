<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
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
                    {!! Form::label('make', ucfirst(__('make')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('make', $trailer->make ?? null, ['class' => 'form-control' . ($errors->first('make') ? ' is-invalid' : '')]) !!}
                    @error('make')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('model', ucfirst(__('model')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('model', $trailer->model ?? null, ['class' => 'form-control' . ($errors->first('model') ? ' is-invalid' : '')]) !!}
                    @error('model')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('year', ucfirst(__('year')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('year', $trailer->year ?? null, ['class' => 'form-control' . ($errors->first('year') ? ' is-invalid' : '')]) !!}
                    @error('year')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
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
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
