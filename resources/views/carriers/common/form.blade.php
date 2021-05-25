<!--<div class="row">
    <div class="col-6">-->
<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $carrier->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('email', ucfirst(__('email')), ['class' => 'col-form-label']) !!}
                    {!! Form::email('email', $carrier->email ?? null, ['class' => 'form-control' . ($errors->first('email') ? ' is-invalid' : '')]) !!}
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
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('phone', ucfirst(__('phone')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('phone', $carrier->phone ?? null, ['class' => 'form-control' . ($errors->first('phone') ? ' is-invalid' : '')]) !!}
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('address', ucfirst(__('address')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('address', $carrier->address ?? null, ['class' => 'form-control' . ($errors->first('address') ? ' is-invalid' : '')]) !!}
                    @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('city', ucfirst(__('city')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('city', $carrier->city ?? null, ['class' => 'form-control' . ($errors->first('city') ? ' is-invalid' : '')]) !!}
                    @error('city')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('state', ucfirst(__('state')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('state', $carrier->state ?? null, ['class' => 'form-control' . ($errors->first('state') ? ' is-invalid' : '')]) !!}
                    @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('zip_code', ucfirst(__('zip code')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('zip_code', $carrier->phone ?? null, ['class' => 'form-control' . ($errors->first('zip_code') ? ' is-invalid' : ''), 'maxlength' => 5]) !!}
                    @error('zip_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <fieldset>
                        {!! Form::label('inactive', ucfirst(__('inactive')), ['class' => 'col-form-label']) !!}
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            {{ Form::checkbox('inactive', 1, $carrier->inactive ?? null) }}
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
</div>
</div> <!-- end card -->
<!--<div class="col-6">
    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div class="form-group">
                    <button class="btn btn-success" type="button">Add Paperwork</button>
                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Documento</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Archivo 2223</td>
                        <td>Safety</td>
                        <td><a href="http://google.com">url</a></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>-->
