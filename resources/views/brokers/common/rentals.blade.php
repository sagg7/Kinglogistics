<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-12">
                    {!! Form::label('rental_inspection_check_out_annex', ucfirst(__('Rental check in pdf annex')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('rental_inspection_check_out_annex', $company->config->rental_inspection_check_out_annex ?? null, ['class' => 'form-control' . ($errors->first('rental_inspection_check_out_annex') ? ' is-invalid' : ''), 'maxlength' => '25000']) !!}
                    @error('rental_inspection_check_out_annex')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-12">
                    {!! Form::label('rental_inspection_check_in_annex', ucfirst(__('Rental check out pdf annex')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('rental_inspection_check_in_annex', $company->config->rental_inspection_check_in_annex ?? null, ['class' => 'form-control' . ($errors->first('rental_inspection_check_in_annex') ? ' is-invalid' : ''), 'maxlength' => '25000']) !!}
                    @error('rental_inspection_check_in_annex')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block submit-ajax', 'type' => 'submit']) !!}
    </div> <!-- end card-body -->
</div> <!-- end card -->
