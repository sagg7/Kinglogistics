<div class="card">
    <div class="card-body">
        <div class="card-content">
            <div class="row">
                <div class="form-group col-3">
                    {!! Form::label('carrier_id', ucfirst(__('carrier')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('carrier_id', [], $message->carrier_id ?? null, ['class' => 'form-control' . ($errors->first('carrier_id') ? ' is-invalid' : '')]) !!}
                    @error('carrier_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-3">
                    {!! Form::label('zone_id', ucfirst(__('zone')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('zone_id', [], $message->zone_id ?? null, ['class' => 'form-control' . ($errors->first('zone_id') ? ' is-invalid' : '')]) !!}
                    @error('zone_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-3">
                    {!! Form::label('turn_id', ucfirst(__('turn')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('turn_id', $turns, $message->turn_id ?? null, ['class' => 'form-control' . ($errors->first('turn_id') ? ' is-invalid' : '')]) !!}
                    @error('turn_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-3">
                    {!! Form::label('drivers', ucfirst(__('drivers')), ['class' => 'col-form-label']) !!}
                    {!! Form::select('drivers[]', [], $message->turn_id ?? null, ['class' => 'form-control' . ($errors->first('drivers') ? ' is-invalid' : ''), 'multiple']) !!}
                    @error('drivers')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ ucfirst($message) }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-12">
                    {!! Form::label('title', ucfirst(__('title')), ['class' => 'col-form-label']) !!}
                    {!! Form::text('title', $message->title ?? null, ['class' => 'form-control' . ($errors->first('title') ? ' is-invalid' : '')]) !!}
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
