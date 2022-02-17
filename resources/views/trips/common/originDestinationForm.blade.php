<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="form-group">
                {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-map-marker-alt"></i></span>
                    </div>
                    {!! Form::text('name', $model->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        {!! Form::label('lat', 'Latitude', ['class' => 'col-form-label']) !!}
                        {!! Form::text('lat', $model->lat ?? null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        {!! Form::label('lng', 'Longitude', ['class' => 'col-form-label']) !!}
                        {!! Form::text('lng', $model->lng ?? null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::hidden('coords', $model->coords ?? null, ['class' => 'form-control' . ($errors->first('coords') ? ' is-invalid' : '')]) !!}
                @error('coords')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ ucfirst($message) }}</strong>
                </span>
                @enderror
                <div class="div" id="map" style="width: 100%; height: 550px;"></div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
