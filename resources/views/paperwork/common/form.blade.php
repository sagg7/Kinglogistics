<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('mode', 'Paperwork Type', ['class' => 'col-form-label']) !!}
                    {!! Form::select('mode', $mode, $paperwork->mode ?? null, ['class' => 'form-control' . ($errors->first('mode') ? ' is-invalid' : '')]) !!}
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('type', 'Section Type', ['class' => 'col-form-label']) !!}
                    {!! Form::select('type', $types, $paperwork->type ?? null, ['class' => 'form-control' . ($errors->first('type') ? ' is-invalid' : '')]) !!}
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('name', ucfirst(__('name')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('name', $paperwork->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <fieldset>
                        {!! Form::label('required', ucfirst(__('required')), ['class' => 'col-form-label']) !!}
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            {{ Form::checkbox('required', 1, $paperwork->required ?? null) }}
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('shipper_id', ucfirst(__('shipper')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('shipper_id', isset($paperwork->shipper_id) ? [$paperwork->shipper_id => $paperwork->shipper->name] : [], $paperwork->shipper_id ?? null, ['class' => 'form-control', 'disabled' . ($errors->first('shipper_id') ? ' is-invalid' : '')]) !!}
                </div>
                <div class="form-group col-6" id="simpleTemplate">
                    {!! Form::label('file', ucfirst(__('template file')), ['class' => 'col-form-label d-block']) !!}
                    <label for="file" class="btn btn-success btn-block">Upload file</label>
                    {!! Form::file('file', ['class' => 'd-none', ($errors->first('file') ? ' is-invalid' : '')]) !!}
                    @isset($paperwork->file)
                        <a href="{{ route('s3storage.temporaryUrl', ['url' => $paperwork->file]) }}" target="_blank">{{ $paperwork->file_name }}</a>
                    @endisset
                </div>
                <div class="col-12 d-none" id="advancedTemplate">
                    <div class="form-group">
                        {!! Form::label('images', ucfirst(__('Upload images')), ['class' => 'col-form-label d-block']) !!}
                        {!! Form::file('images[]', [($errors->first('images') ? ' is-invalid' : ''), 'multiple', 'accept' => 'image/jpeg, image/png', 'disabled', 'id' => 'imagesInput']) !!}
                        <ul id="imagesList" style="font-size: 1.3rem;">
                            @isset($paperwork)
                                @foreach($paperwork->images as $image)
                                    <li>
                                        <code id="img_{{ $image->number }}">{{ '{{"image":"' . $image->number . '"}' . '}' }}</code>
                                        <button type="button" class="btn btn-danger deleteImage" data-imageid="{{ $image->id }}" style="padding: 3px 5px;"><i class="fas fa-times"></i></button>
                                    </li>
                                @endforeach
                            @endisset
                        </ul>
                    </div>

                    <div class="form-group">
                        {!! Form::label('template', ucfirst(__('template')), ['class' => 'col-form-label']) !!} <i class="fas fa-info-circle cursor-pointer" data-toggle="modal" data-target="#infoModal"></i>
                        {!! Form::textarea('template', $paperwork->template ?? null, ['class' => 'form-control' . ($errors->first('template') ? ' is-invalid' : ''), 'disabled']) !!}
                        @error('template')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div>
