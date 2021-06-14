<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                @if(auth()->guard('carrier')->check())
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
                    {!! Form::select('zone_id', [], $driver->zone_id ?? null, ['class' => 'form-control' . ($errors->first('zone_id') ? ' is-invalid' : '')]) !!}
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
            @if(auth()->guard('carrier')->check())
            <hr>
            <div class="row">
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
            </div>
            @endif
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
