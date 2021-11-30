<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                @if(auth()->guard('web')->check())
                    <div class="form-group col-md-6">
                        {!! Form::label('carrier_id', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('carrier_id', isset($driver) ? [$driver->carrier_id => $driver->carrier->name] : [], $driver->carrier_id ?? null, ['class' => 'form-control' . ($errors->first('carrier_id') ? ' is-invalid' : '')]) !!}
                        @error('carrier_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('shippers[]', ucfirst(__('shippers')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('shippers[]', [], $driver->shippers ?? null, ['class' => 'form-control' . ($errors->first('shippers') ? ' is-invalid' : ''), 'multiple']) !!}
                        @error('shippers')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('turn_id', ucfirst(__('turn')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('turn_id', $turns, $driver->turn_id ?? null, ['class' => 'form-control select2' . ($errors->first('turn_id') ? ' is-invalid' : '')]) !!}
                        @error('turn_id')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        {!! Form::label('zone_id', ucfirst(__('zone')), ['class' => 'col-form-label']) !!}
                        {!! Form::select('zone_id', $zones, $driver->zone_id ?? null, ['class' => 'form-control' . ($errors->first('zone_id') ? ' is-invalid' : '')]) !!}
                        @error('zone_id')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                        @enderror
                    </div>
                @endif
                <div class="form-group col-md-6">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $driver->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('email', ucfirst(__('email')), ['class' => 'col-form-label']) !!}
                    {!! Form::email('email', $driver->email ?? null, ['class' => 'form-control' . ($errors->first('email') ? ' is-invalid' : '')]) !!}
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('password', ucfirst(__('password')), ['class' => 'col-form-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control' . ($errors->first('password') ? ' is-invalid' : '')]) !!}
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('password_confirmation', ucfirst(__('password confirmation')), ['class' => 'col-form-label']) !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control' . ($errors->first('password_confirmation') ? ' is-invalid' : '')]) !!}
                    @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                    <hr>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('phone', ucfirst(__('phone')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('phone', $driver->phone ?? null, ['class' => 'form-control' . ($errors->first('phone') ? ' is-invalid' : '')]) !!}
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('address', ucfirst(__('address')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('address', $driver->address ?? null, ['class' => 'form-control' . ($errors->first('address') ? ' is-invalid' : '')]) !!}
                    @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <fieldset>
                        {!! Form::label('inactive', ucfirst(__('inactive')), ['class' => 'col-form-label']) !!}
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            {{ Form::checkbox('inactive', 1, $driver->inactive ?? null) }}
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-12">
                    {!! Form::label('inactive_observations', ucfirst(__('inactive observations')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('inactive_observations', $driver->inactive_observations, ['class' => 'form-control', 'rows' => 5, 'maxlength' => 512]) !!}
                    @error('inactive_observations')
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
