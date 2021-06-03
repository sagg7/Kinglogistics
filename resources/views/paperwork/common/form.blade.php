<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('mode', 'Paperwork Type') !!}
                    {!! Form::select('mode', $mode, $paperwork->mode ?? null, ['class' => 'form-control' . ($errors->first('period') ? ' is-invalid' : '')]) !!}
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('type', 'Section Type') !!}
                    {!! Form::select('type', $types, $paperwork->type ?? null, ['class' => 'form-control' . ($errors->first('period') ? ' is-invalid' : '')]) !!}
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
                <div class="form-group col d-none">
                    {!! Form::label('template', ucfirst(__('template')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('template', $paperwork->template ?? null, ['class' => 'form-control' . ($errors->first('template') ? ' is-invalid' : ''), 'disabled']) !!}
                    @error('template')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div>
