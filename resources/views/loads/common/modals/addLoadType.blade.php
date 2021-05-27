<div class="modal fade" id="addLoadType" tabindex="-1" role="dialog" aria-labelledby="load-type-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="load-type-modal-label">New Load Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'loadType.store', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'data-target-select' => '#load_type_id']) !!}

                <div class="form-content">

                    <div class="form-group">
                        <label>Name</label>
                        <input id="name" class="form-control" name="name" type="text">
                        @if(auth()->guard('web')->check())
                            <input id="load_type_shipper" class="form-control" name="shipper" type="hidden">
                        @endif
                    </div>

                </div>

                <a class="d-block text-muted float-right mb-2" href="#deleteLoadType" data-dismiss="modal" data-toggle="modal" data-target="#deleteLoadType">
                    <span>Delete options</span>
                </a>

                <button type="submit" class="btn btn-block btn-success">Insert</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
