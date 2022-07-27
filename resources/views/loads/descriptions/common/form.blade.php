<div class="row">
    <div class="form-group col-md col-sm-12">
        {!! Form::label('name', ucfirst(__('Name')), ['class' => 'col-form-label']) !!}
        {!! Form::text('name', $description->name ?? null, ['class' => 'form-control' . ($errors->first('name') ? ' is-invalid' : '')]) !!}
        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ ucfirst($message) }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group col-md col-sm-12">
        {!! Form::label('name_spanish', ucfirst(__('Spanish name')), ['class' => 'col-form-label']) !!}
        {!! Form::text('name_spanish', $description->name_spanish ?? null, ['class' => 'form-control' . ($errors->first('name_spanish') ? ' is-invalid' : '')]) !!}
        @error('name_spanish')
        <span class="invalid-feedback" role="alert">
            <strong>{{ ucfirst($message) }}</strong>
        </span>
        @enderror
    </div>
    <div class="form-group col-md-12 col-sm-12">
        {!! Form::label('text', ucfirst(__('Text')), ['class' => 'col-form-label']) !!}
        {!! Form::textarea('text', $description->text ?? null, ['class' => 'form-control' . ($errors->first('text') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 1024]) !!}
        @error('text')
        <span class="invalid-feedback" role="alert">
            <strong>{{ ucfirst($message) }}</strong>
        </span>
        @enderror
    </div>
</div>
{!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
