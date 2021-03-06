<div class="modal fade table-responsive" id="postLoadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Post Load</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'roadLoads.store', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'id' => 'formRoadLoads']) !!}

                <div class="row">
                    <div class="form-group col-lg-4">
                        @if(auth()->guard('web')->check())
                            <h2 class="text-center">Customer</h2>
                            <div class="row">
                                <div class="form-group col-md-6 col-12">
                                    {!! Form::label('shipper', ucfirst(__('customer')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('shipper', [], $driver->shippers ?? null, ['class' => 'form-control' . ($errors->first('shippers') ? ' is-invalid' : '')]) !!}
                                    @error('shippers')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ ucfirst($message) }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <h2 class="text-center">Origin</h2>
                        <div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('statesOrigin', ucfirst(__('State')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('statesOrigin', [], null, ['class' => 'form-control' . ($errors->first('states') ? ' is-invalid' : '')]) !!}
                                    @error('statesOrigin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
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
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('origin_early_pick_up_date', ucwords(__('Early Pick Up Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('origin_early_pick_up_date', null, ['class' => 'form-control']) !!}
                                    @error('origin_early_pick_up_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                    @enderror
                                </fieldset>
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('origin_late_pick_up_date', ucwords(__('Late Pick Up Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('origin_late_pick_up_date', null, ['class' => 'form-control']) !!}
                                    @error('origin_late_pick_up_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                    @enderror
                                </fieldset>
                            </div>
                            <h2 class="text-center">Destination</h2>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('stateDestination', ucfirst(__('State')), ['class' => 'col-form-label']) !!}
                                    {!! Form::select('stateDestination', [], null, ['class' => 'form-control' . ($errors->first('stateDestination') ? ' is-invalid' : '')]) !!}
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
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('destination_early_drop_off_date', ucwords(__('Early Drop Off Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('destination_early_drop_off_date', null, ['class' => 'form-control']) !!}
                                    @error('destination_early_drop_off_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                    @enderror
                                </fieldset>
                                <fieldset class="form-group col-md-6">
                                    {!! Form::label('destination_late_drop_off_date', ucwords(__('Late Drop Off Date')), ['class' => 'col-form-label']) !!}
                                    {!! Form::text('destination_late_drop_off_date', null, ['class' => 'form-control']) !!}
                                    @error('destination_late_drop_off_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ ucfirst($message) }}</strong>
                                    </span>
                                    @enderror
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <h2 class="text-center">Equipment & Load Details</h2>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! Form::label('trailer_type_id', ucwords(__('Trailer type')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('trailer_type_id', [], null, ['class' => 'form-control']) !!}
                                @error('trailer_type_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                {!! Form::label('mode_id', ucfirst(__('Mode')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('mode_id', [], null, ['class' => 'form-control']) !!}
                                @error('mode_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
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
                                @error('rate')
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
                                @error('weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('tons', ucwords(__('tons')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('tons', null, ['class' => 'form-control']) !!}
                                @error('tons')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('width', ucwords(__('width')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('width', null, ['class' => 'form-control']) !!}
                                @error('width')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('height', ucwords(__('height')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('height', null, ['class' => 'form-control']) !!}
                                @error('height')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('length', ucwords(__('length')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('length', null, ['class' => 'form-control']) !!}
                                @error('length')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                            {{-- <fieldset class="form-group col-md-6">
                                {!! Form::label('cube', ucwords(__('cube')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('cube', null, ['class' => 'form-control']) !!}
                            </fieldset> --}}
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('pieces', ucwords(__('pieces')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('pieces', null, ['class' => 'form-control']) !!}
                                @error('pieces')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('pallets', ucwords(__('pallets')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('pallets', null, ['class' => 'form-control']) !!}
                                @error('pallets')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {{-- load_type_id --}}
                                {!! Form::label('load_type_id', ucwords(__('Load type')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('load_type_id', [], null, ['class' => 'form-control']) !!}
                                @error('load_type_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-lg-4">
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('mileage', ucwords(__('distance')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('mileage', null, ['class' => 'form-control']) !!}
                                @error('mileage')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('silo_number', ucwords(__('Silo number')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('silo_number', null, ['class' => 'form-control']) !!}
                                @error('silo_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('customer_po', ucwords(__(session('renames')->po ?? 'Customer PO')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('customer_po', null, ['class' => 'form-control']) !!}
                                @error('customer_po')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>

                            <fieldset class="form-group col-md-6">
                                {!! Form::label('control_number', ucwords(__('Load Number')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('control_number', null, ['class' => 'form-control']) !!}
                                @error('control_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="row">
                            <fieldset class="form-group col-md-6">
                                {!! Form::label('pay_rate', ucwords(__('Payrate')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('pay_rate', null, ['class' => 'form-control']) !!}
                                @error('pay_rate')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                            <fieldset class="form-group col-md-6">
                                {{-- load_type_id --}}
                                {!! Form::label('load_size', ucwords(__('load size')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('load_size', $load_sizes, null, ['class' => 'form-control']) !!}
                                @error('load_size')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>
                        <!--<div class="row">
                            <fieldset class="form-group col-md-12">
                                {!! Form::label('days_to_pay', ucwords(__('days until payment')), ['class' => 'col-form-label']) !!}
                                {!! Form::text('days_to_pay', null, ['class' => 'form-control']) !!}
                                @error('days_to_pay')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </fieldset>
                        </div>-->
                        <div class="row">
                            <div class="form-group col-md-12">
                                {!! Form::label('notes', ucfirst(__('description')), ['class' => 'col-form-label']) !!}
                                {!! Form::textarea('notes', null, ['class' => 'form-control' . ($errors->first('notes') ? ' is-invalid' : ''), 'rows' => 5, 'maxlength' => 512]) !!}
                                @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ ucfirst($message) }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-block btn-primary">Insert</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
