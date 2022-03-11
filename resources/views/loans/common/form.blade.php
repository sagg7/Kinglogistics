<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('carrier', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('carrier', [], $loan->carrier_id ?? null, ['class' => 'form-control' . ($errors->first('carrier') ? ' is-invalid' : '')]) !!}
                    @error('carrier')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <fieldset>
                        {!! Form::label('amount', ucfirst(__('amount')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            {!! Form::text('amount', $loan->amount ?? null, ['class' => 'form-control' . ($errors->first('amount') ? ' is-invalid' : '')]) !!}
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-4">
                    <fieldset>
                        {!! Form::label('fee_percentage', ucfirst(__('fee percentage')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-percent"></i></span>
                            </div>
                            {!! Form::text('fee_percentage', $loan->amount ?? null, ['class' => 'form-control' . ($errors->first('fee_percentage') ? ' is-invalid' : '')]) !!}
                            @error('fee_percentage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-4">
                    {!! Form::label('installments', ucfirst(__('installments')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('installments', $loan->installments ?? null, ['class' => 'form-control' . ($errors->first('installments') ? ' is-invalid' : '')]) !!}
                    @error('installments')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <fieldset>
                        {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            {!! Form::text("date", $loan->date ?? null, ['class' => 'form-control pickadate-months-year']) !!}
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>

                <div class="form-group col-md-4">
                            <fieldset>
                            {!! Form::label('file_loan', "Add File", ['class' => 'col-form-label']) !!}
                            <div class="file-group">
                                <label for="file_loan" class="btn form-control btn-primary btn-block">
                                    <i class="fas fa-file"></i> <span class="file-name">Upload File</span>
                                    <input type="file" name="file_loan" id="file_loan" hidden>
                                </label>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            @isset($loan->file_loan_url)
                                <a href="{{ route('s3storage.temporaryUrl', ['url' =>  $loan->file_loan_url]) }}" target="_blank">{{ $loan->loan_file_name}}</a>
                            @endisset
                            </fieldset>
                </div>
                    
                <div class="form-group col-md-12">
                    {!! Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('description', $loan->description ?? null, ['class' => 'form-control' . ($errors->first('description') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 512]) !!}
                    @error('description')
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
