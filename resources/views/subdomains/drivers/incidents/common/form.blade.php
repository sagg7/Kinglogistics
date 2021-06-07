<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-3">
                    {!! Form::label('incident_type_id', ucfirst(__('incident type')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::text('incident_type_id', $incident->incident_type->name ?? null, ['class' => 'form-control', 'readonly']) !!}
                    </div>
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('sanction', ucfirst(__('sanction')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('sanction', $incident->sanction_name ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('date', $incident->date->toFormattedDateString() ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('location', ucfirst(__('location')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('location', $incident->location ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('carrier_id', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('carrier_id', $incident->carrier->name ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('driver_id', ucfirst(__('driver')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('driver_id', $incident->driver->name ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('truck_id', ucfirst(__('truck')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('truck_id', $incident->truck->number ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-3">
                    {!! Form::label('trailer_id', ucfirst(__('trailer')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('trailer_id', $incident->trailer->number ?? null, ['class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('description', $incident->description ?? null, ['class' => 'form-control', 'rows' => 5, 'readonly']) !!}
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('excuse', ucfirst(__('driver excuse')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('excuse', $incident->excuse ?? null, ['class' => 'form-control', 'rows' => 5, 'readonly']) !!}
                </div>
                <div class="form-group col-md-6 text-center">
                    <img src="{{ asset($incident->safety_signature) }}" alt="Safety Signature" id="safety_signature">
                    <label class="col-form-label d-block" for="safety_signature">Safety Signature</label>
                </div>
                <div class="form-group col-md-6 text-center">
                    <label class="col-form-label d-block" for="driver_signature">Driver Signature</label>
                    <div>
                        <canvas class="d-block mx-auto" id="driver_signature"></canvas>
                        <button type="button" class="btn btn-outline-danger mt-1">Clear</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
