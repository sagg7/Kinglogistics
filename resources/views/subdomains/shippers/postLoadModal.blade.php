<div class="modal fade table-responsive" id="postLoadModal" tabindex="-1" role="dialog" aria-hidden="true" style="max-height: calc(100vh - 4.5rem);">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content "   >
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Post Load</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'roadLoads.store', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'id' => 'formRoadLoads']) !!}

                <div class="row">
                    <div class="form-group col-md-4">
                        <div class="row">
                            <h2 class="col-md-12 text-center">Origin</h2>
                        </div>
                        <div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('statesOrigin', ucfirst(__('State')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('statesOrigin', [],  null, ['class' => 'form-control' . ($errors->first('states') ? ' is-invalid' : '')]) !!}
                                    @error('carriers')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ ucfirst($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class=" form-group col-md-6">
                                    {!! Form::label('citiesOrigin', ucfirst(__('city Origin')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('citiesOrigin', [], null, ['class' => 'form-control' . ($errors->first('citiesOrigin') ? ' is-invalid' : '')]) !!}
                                    @error('citiesOrigin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ ucfirst($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('origin_early_pick_up_date', ucwords(__('Early Pick Up Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('origin_early_pick_up_date', null, ['class' => 'form-control']) !!}
                                </fieldset>
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('origin_late_pick_up_date', ucwords(__('Late Pick Up Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('origin_late_pick_up_date', null, ['class' => 'form-control']) !!}
                                </fieldset>
                            </div>
                            <div class="row">
                                <h2 class="col-md-12 text-center">Destination</h2>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('stateDestination', ucfirst(__('State')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('stateDestination', [],  null, ['class' => 'form-control' . ($errors->first('stateDestination') ? ' is-invalid' : '')]) !!}
                                    @error('stateDestination')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ ucfirst($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('cityDestination', ucfirst(__('city')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('cityDestination', [], null, ['class' => 'form-control' . ($errors->first('cityDestination') ? ' is-invalid' : '')]) !!}
                                    @error('cityDestination')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ ucfirst($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('destination_early_pick_up_date', ucwords(__('Early Pick Up Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('destination_early_pick_up_date', null, ['class' => 'form-control']) !!}
                                </fieldset>
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('destination_late_pick_up_date', ucwords(__('Late Pick Up Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('destination_late_pick_up_date', null, ['class' => 'form-control']) !!}
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="row">
                            <h2 class="col-md-12 text-center">Equipment & Load Details</h2>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! Form::label('trailer_type_id', ucwords(__('Trailer type')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('trailer_type_id', [], null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('mode_id', ucfirst(__('Mode')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('mode_id',[], null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! Form::label('shipper_rate', ucwords(__('broker rate')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('shipper_rate', $shipper->loads_per_invoice ?? null, ['class' => 'form-control' . ($errors->first('shipper_rate') ? ' is-invalid' : '')]) !!}
                                @error('shipper_rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('rate', ucwords(__('carrier rate')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('rate', $shipper->loads_per_invoice ?? null, ['class' => 'form-control' . ($errors->first('rate') ? ' is-invalid' : '')]) !!}
                                @error('loads_per_invoice')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('weight', ucwords(__('weight')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('tons', ucwords(__('tons')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('tons', null, ['class' => 'form-control']) !!}
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('width', ucwords(__('width')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('width', null, ['class' => 'form-control']) !!}
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('height', ucwords(__('height')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('height', null, ['class' => 'form-control']) !!}
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('length', ucwords(__('length')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('length', null, ['class' => 'form-control']) !!}
                            </fieldset>
                            {{-- <fieldset class="form-group col-md-6">
                                {!! Form::label('cube', ucwords(__('cube')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('cube', null, ['class' => 'form-control']) !!}
                            </fieldset> --}}
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('pieces', ucwords(__('pieces')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('pieces', null, ['class' => 'form-control']) !!}
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('pallets', ucwords(__('pallets')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('pallets', null, ['class' => 'form-control']) !!}
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {{-- load_type_id --}}
                                {!! Form::label('load_type_id', ucwords(__('Load type')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('load_type_id', [], null , ['class' => 'form-control']) !!}
                            </fieldset>
                        </div>

                    </div>
                    <div class="form-group col-md-4">
                        <div class="row">
                            <br>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('mileage', ucwords(__('distance')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('mileage', null, ['class' => 'form-control']) !!}
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('silo_number', ucwords(__('Silo number')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('silo_number', null, ['class' => 'form-control']) !!}
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('customer_po', ucwords(__('customer PO')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('customer_po', null, ['class' => 'form-control']) !!}
                            </fieldset>

                            <fieldset class="form-group col-md-6">
                                {!! Form::label('control_number', ucwords(__('Load Number')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('control_number', null, ['class' => 'form-control']) !!}
                            </fieldset>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                {!! Form::label('notes', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                                {!! Form::textarea('notes', null, ['class' => 'form-control' . ($errors->first('notes') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 512]) !!}
                                {{-- @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                @enderror --}}
                            </div>
                        </div>
                        <div class="row">
                            <h2 class="col-md-12 text-center">Additional Details</h2>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! Form::label('sales', ucwords(__('Sales')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('sales',  ['1' => '1', '2' => '2'], null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
           
                    </div>
                    <button type="submit" class="btn btn-block btn-success">Insert</button>

                   
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
