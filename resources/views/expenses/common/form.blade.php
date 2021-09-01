<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-4">
                    {!! Form::label('type', ucfirst(__('type')), ['class' => 'col-form-label']) !!}
                    <div class="input-group">
                        {!! Form::select('type', $types, $expense->type_id ?? null, ['class' => 'form-control' . ($errors->first('type') ? ' is-invalid' : '')]) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success pl-1 pr-1" type="button" data-toggle="modal" data-target="#expenseTypeModal"><i class="fas fa-plus"></i></button>
                        </div>
                        @error('type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ ucfirst($message) }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <fieldset>
                        {!! Form::label('amount', ucfirst(__('amount')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            {!! Form::text('amount', $expense->amount ?? null, ['class' => 'form-control' . ($errors->first('amount') ? ' is-invalid' : '')]) !!}
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
                        {!! Form::label('date', ucfirst(__('date')), ['class' => 'col-form-label']) !!}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            {!! Form::text("date", $expense->date ?? null, ['class' => 'form-control pickadate-months-year']) !!}
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ ucfirst($message) }}</strong>
                            </span>
                            @enderror
                        </div>
                    </fieldset>
                </div>
                <div class="form-group col-md-12">
                    {!! Form::label('description', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                    {!! Form::textarea('description', $expense->description ?? null, ['class' => 'form-control' . ($errors->first('description') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 512]) !!}
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
