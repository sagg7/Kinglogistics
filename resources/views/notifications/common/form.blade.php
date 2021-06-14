<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-12">
                    {!! Form::label('title', ucfirst(__('title')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('title', $notification->title ?? null, ['class' => 'form-control' . ($errors->first('title') ? ' is-invalid' : '')]) !!}
                    @error('title')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-12">
                    <div>
                        {!! Form::label('message', ucfirst(__('message')), ['class' => 'col-form-label']) !!}
                        <div id="message_quill" style="height: calc(100vh - 505px);"></div>
                        @error('message')
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
</div> <!-- end card -->
<!--<div id="message" style="height: calc(100vh - 505px); min-height: 300px;"></div>-->
