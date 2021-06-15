<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                @if(auth()->guard('web')->check())
                    <div class="form-group col-md-6">
                        {!! Form::label('carrier_id', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('carrier_id', $carriers, $truck->carrier_id ?? null, ['class' => 'form-control' . ($errors->first('carrier_id') ? ' is-invalid' : '')]) !!}
                        @error('carrier_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('seller_id', ucfirst(__('seller')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('seller_id', $sellers, $truck->seller_id ?? null, ['class' => 'form-control' . ($errors->first('seller_id') ? ' is-invalid' : '')]) !!}
                        @error('seller_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                @endif
                @if(auth()->guard('carrier')->check())
                    <div class="form-group col-md-6">
                        {!! Form::label('trailer_id', ucfirst(__('trailer')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('trailer_id', [], $truck->trailer_id ?? null, ['class' => 'form-control' . ($errors->first('trailer_id') ? ' is-invalid' : '')]) !!}
                        @error('trailer_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('driver_id', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('driver_id', [], $truck->driver_id ?? null, ['class' => 'form-control' . ($errors->first('driver_id') ? ' is-invalid' : '')]) !!}
                        @error('driver_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                @endif
                <div class="form-group col-md-6">
                    {!! Form::label('number', ucfirst(__('number')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('number', $truck->number ?? null, ['class' => 'form-control' . ($errors->first('number') ? ' is-invalid' : '')]) !!}
                    @error('number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('plate', ucfirst(__('plate')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('plate', $truck->plate ?? null, ['class' => 'form-control' . ($errors->first('plate') ? ' is-invalid' : '')]) !!}
                    @error('plate')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('vin', ucfirst(__('vin')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('vin', $truck->plate ?? null, ['class' => 'form-control' . ($errors->first('vin') ? ' is-invalid' : '')]) !!}
                    @error('vin')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('make', ucfirst(__('make')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('make', $truck->make ?? null, ['class' => 'form-control' . ($errors->first('make') ? ' is-invalid' : '')]) !!}
                    @error('make')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('model', ucfirst(__('model')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('model', $truck->model ?? null, ['class' => 'form-control' . ($errors->first('model') ? ' is-invalid' : '')]) !!}
                    @error('model')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('year', ucfirst(__('year')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('year', $truck->year ?? null, ['class' => 'form-control' . ($errors->first('year') ? ' is-invalid' : '')]) !!}
                    @error('year')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-6">
                    <fieldset>
                        {!! Form::label('inactive', ucfirst(__('inactive')), ['class' => 'col-form-label']) !!}
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            {{ Form::checkbox('inactive', 1, $truck->inactive ?? null) }}
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
