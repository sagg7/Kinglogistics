<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('incident_type_id', ucfirst(__('incident type')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::select('incident_type_id', $incident_types, $incident->incident_type_id ?? null, ['class' => 'form-control' . ($errors->first('incident_type_id') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" data-toggle="modal" data-target="#addIncidentType"><i class="fas fa-plus"></i></button>
                        </div>
                        @error('incident_type_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('driver_id', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('driver_id', [], $incident->driver_id ?? null, ['class' => 'form-control' . ($errors->first('driver_id') ? ' is-invalid' : '')]) !!}
                    @error('driver_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('sanction', ucfirst(__('sanction')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('sanction', $sanctions, $incident->sanction ?? null, ['class' => 'form-control' . ($errors->first('sanction') ? ' is-invalid' : '')]) !!}
                    @error('sanction')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('safety_description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('safety_description', $incident->safety_description ?? null, ['class' => 'form-control' . ($errors->first('safety_description') ? ' is-invalid' : ''), 'rows' => 5]) !!}
                    @error('safety_description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('driver_description', ucfirst(__('driver description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('driver_description', $incident->driver_description ?? null, ['class' => 'form-control' . ($errors->first('driver_description') ? ' is-invalid' : ''), 'rows' => 5]) !!}
                    @error('driver_description')
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
