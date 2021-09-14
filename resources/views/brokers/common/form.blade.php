<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('name', ucfirst(__('company name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $company->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('contact_phone', ucfirst(__('contact phone')), ['class' => 'col-form-label']) !!}
                    {!! Form::tel('contact_phone', $company->contact_phone ?? null, ['class' => 'form-control' . ($errors->first('contact_phone') ? ' is-invalid' : '')]) !!}
                    @error('contact_phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group @isset($roles){{ "col-md-12" }}@else{{ "col-md-6" }}@endif">
                    {!! Form::label('email', ucfirst(__('email')), ['class' => 'col-form-label']) !!}
                    {!! Form::email('email', $company->email ?? null, ['class' => 'form-control' . ($errors->first('email') ? ' is-invalid' : '')]) !!}
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('dot_number', "DOT number", ['class' => 'col-form-label']) !!}
                    {!! Form::tel('dot_number', $company->dot_number ?? null, ['class' => 'form-control' . ($errors->first('dot_number') ? ' is-invalid' : '')]) !!}
                    @error('dot_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('mc_number', "MC number", ['class' => 'col-form-label']) !!}
                    {!! Form::tel('mc_number', $company->mc_number ?? null, ['class' => 'form-control' . ($errors->first('mc_number') ? ' is-invalid' : '')]) !!}
                    @error('mc_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('insurance', "Insurance", ['class' => 'col-form-label']) !!}
                    <div class="file-group">
                        <label for="insurance" class="btn form-control btn-primary btn-block">
                            <i class="fas fa-file"></i> <span class="file-name">Upload File</span>
                            <input type="file" name="insurance" id="insurance" hidden>
                        </label>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    @isset($company->insurance_url)
                        <a class="d-block mt-2" href="{{ $company->insurance_url }}" target="_blank">Uploaded file: {{ $company->insurance_file_name }}</a>
                    @endisset
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-12">
                    {!! Form::label('address', "Address", ['class' => 'col-form-label']) !!}
                    {!! Form::tel('address', $company->address ?? null, ['class' => 'form-control' . ($errors->first('address') ? ' is-invalid' : '')]) !!}
                    @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-12">
                    {!! Form::hidden('coords', $company->location ?? null) !!}
                    <div id="mapLocation" style="width: 100%; height: 550px;"></div>
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
