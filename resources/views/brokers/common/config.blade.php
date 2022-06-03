<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('job', ucfirst(__('job')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('job', $renames->job ?? null, ['class' => 'form-control' . ($errors->first('job') ? ' is-invalid' : '')]) !!}
                    @error('job')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('control_number', ucfirst(__('control number')), ['class' => 'col-form-label']) !!}
                    {!! Form::tel('control_number', $renames->control_number ?? null, ['class' => 'form-control' . ($errors->first('control_number') ? ' is-invalid' : '')]) !!}
                    @error('control_number')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('customer_reference', ucfirst(__('customer reference')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('customer_reference', $renames->customer_reference ?? null, ['class' => 'form-control' . ($errors->first('customer_reference') ? ' is-invalid' : '')]) !!}
                    @error('customer_reference')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('bol', ucfirst(__('bol')), ['class' => 'col-form-label']) !!}
                    {!! Form::tel('bol', $renames->bol ?? null, ['class' => 'form-control' . ($errors->first('bol') ? ' is-invalid' : '')]) !!}
                    @error('bol')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('tons', ucfirst(__('tons')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('tons', $renames->tons ?? null, ['class' => 'form-control' . ($errors->first('tons') ? ' is-invalid' : '')]) !!}
                    @error('tons')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('po', ucfirst(__('PO')), ['class' => 'col-form-label']) !!}
                    {!! Form::tel('po', $renames->po ?? null, ['class' => 'form-control' . ($errors->first('po') ? ' is-invalid' : '')]) !!}
                    @error('po')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    {!! Form::label('carrier', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('carrier', $renames->carrier ?? null, ['class' => 'form-control' . ($errors->first('carrier') ? ' is-invalid' : '')]) !!}
                    @error('carrier')
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
