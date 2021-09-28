<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('equipment_type', ucfirst(__('equipment type')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('equipment_type', $equipmentTypes, $equipment->carrier_equipment_type_id ?? null, ['class' => 'form-control' . ($errors->first('equipment_type') ? ' is-invalid' : '')]) !!}
                    @error('equipment_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $equipment->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('status', ucfirst(__('status')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('status', ['available' => 'Available', 'unavailable' => 'Unavailable'], $equipment->status ?? null, ['class' => 'form-control' . ($errors->first('status') ? ' is-invalid' : '')]) !!}
                    @error('status')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    {!! Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('description', $equipment->description ?? null, ['class' => 'form-control' . ($errors->first('description') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 512]) !!}
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
        </div>
    </div> <!-- end card-body -->
</div> <!-- end card -->
