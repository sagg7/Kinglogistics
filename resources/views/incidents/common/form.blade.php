<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('incident_type_id', ucfirst(__('incident type')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::select('incident_type_id', $incident_types, $incident->incident_type_id ?? null, ['class' => 'form-control' . ($errors->first('incident_type_id') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#addIncidentType"><i class="fas fa-plus"></i></button>
                        </div>
                        @error('incident_type_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('sanction', ucfirst(__('sanction')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('sanction', $sanctions, $incident->sanction ?? null, ['class' => 'form-control' . ($errors->first('sanction') ? ' is-invalid' : '')]) !!}
                    @error('sanction')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('date', $incident->date ?? null, ['class' => 'form-control' . ($errors->first('date') ? ' is-invalid' : '')]) !!}
                    @error('date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('location', ucfirst(__('location')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('location', $incident->location ?? null, ['class' => 'form-control' . ($errors->first('location') ? ' is-invalid' : '')]) !!}
                    @error('location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('carrier_id', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('carrier_id', [], $incident->carrier_id ?? null, ['class' => 'form-control' . ($errors->first('carrier_id') ? ' is-invalid' : '')]) !!}
                    @error('carrier_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('driver_id', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('driver_id', [], $incident->driver_id ?? null, ['class' => 'form-control' . ($errors->first('driver_id') ? ' is-invalid' : '')]) !!}
                    @error('driver_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('truck_id', ucfirst(__('truck')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('truck_id', [], $incident->truck_id ?? null, ['class' => 'form-control' . ($errors->first('truck_id') ? ' is-invalid' : '')]) !!}
                    @error('truck_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('trailer_id', ucfirst(__('trailer')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('trailer_id', [], $incident->trailer_id ?? null, ['class' => 'form-control' . ($errors->first('trailer_id') ? ' is-invalid' : '')]) !!}
                    @error('trailer_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('description', $incident->description ?? null, ['class' => 'form-control' . ($errors->first('description') ? ' is-invalid' : ''), 'rows' => 5]) !!}
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('excuse', ucfirst(__('driver excuse')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('excuse', $incident->excuse ?? null, ['class' => 'form-control' . ($errors->first('excuse') ? ' is-invalid' : ''), 'rows' => 5]) !!}
                    @error('excuse')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                @if(!isset($incident))
                <div class="form-group col-md-6 text-center">
                    <label class="col-form-label" for="safety_signature">Safety Signature</label>
                    <div>
                        <canvas class="d-block mx-auto" id="safety_signature"></canvas>
                        <button type="button" class="btn btn-outline-danger mt-1">Clear</button>
                    </div>
                </div>
                <div class="form-group col-md-6 text-center">
                    <label class="col-form-label d-block" for="driver_signature">Driver Signature</label>
                    <div>
                        <canvas class="d-block mx-auto" id="driver_signature"></canvas>
                        <button type="button" class="btn btn-outline-danger mt-1">Clear</button>
                    </div>
                    <fieldset class="d-inline-block mx-auto">
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            {{ Form::checkbox('refuse_sign', 1, $carrier->refuse_sign ?? null) }}
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            {!! Form::label('refuse_sign', ucfirst(__('refuse to sign')), ['class' => 'col-form-label']) !!}
                        </div>
                    </fieldset>
                </div>
                @else
                    <div class="form-group col-md-6 text-center">
                        <img src="{{ $incident->safety_signature }}" alt="Safety Signature" id="safety_signature">
                        <label class="col-form-label d-block" for="safety_signature">Safety Signature</label>
                    </div>
                    <div class="form-group col-md-6 text-center">
                        @if($incident->driver_signature)
                            <img src="{{ $incident->driver_signature }}" alt="Driver Signature" id="driver_signature">
                        @else
                            <div>
                                <canvas class="d-block mx-auto" id="driver_signature"></canvas>
                            </div>
                        @endif
                        <label class="col-form-label d-block" for="driver_signature">Driver Signature</label>
                    </div>
                @endif
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
