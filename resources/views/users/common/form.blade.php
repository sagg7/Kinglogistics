<!--<div class="row justify-content-center">
    <div class="col-xl-8">-->
<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                @isset($roles)
                <div class="form-group col-md-6">
                    {!! Form::label('role', ucfirst(__('role')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('role', $roles, $user->role ?? null, ['class' => 'form-control select2' . ($errors->first('role') ? ' is-invalid' : '')]) !!}
                    @error('role')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @endisset
                <div class="form-group col-md-6">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $user->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('phone', ucfirst(__('phone')), ['class' => 'col-form-label']) !!}
                    {!! Form::tel('phone', $user->phone ?? null, ['class' => 'form-control' . ($errors->first('phone') ? ' is-invalid' : '')]) !!}
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group @isset($roles){{ "col-md-12" }}@else{{ "col-md-6" }}@endif">
                    {!! Form::label('email', ucfirst(__('email')), ['class' => 'col-form-label']) !!}
                    {!! Form::email('email', $user->email ?? null, ['class' => 'form-control' . ($errors->first('email') ? ' is-invalid' : '')]) !!}
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
                <div class="form-group col-md-6">
                    {!! Form::label('turn_start', ucfirst(__('check in time')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('turn_start', $user->turn_start ?? null, ['class' => 'form-control pickatime' . ($errors->first('turn_start') ? ' is-invalid' : '')]) !!}
                    @error('turn_start')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('turn_end', ucfirst(__('check out time')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('turn_end', $user->turn_end ?? null, ['class' => 'form-control pickatime' . ($errors->first('turn_end') ? ' is-invalid' : '')]) !!}
                    @error('turn_end')
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
<!--</div>
</div>-->
