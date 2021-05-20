<div class="modal fade" id="addTrailerType" tabindex="-1" role="dialog" aria-labelledby="trailer-type-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trailer-type-modal-label">New Trailer Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'trailerType.store', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'data-target-select' => '#trailer_type_id']) !!}

                <div class="form-content">

                    <div class="form-group">
                        <label>Name</label>
                        <input id="name" class="form-control" name="name" type="text">
                    </div>

                </div>

                <a class="d-block text-muted float-right mb-2" href="#deleteTrailerType" data-dismiss="modal" data-toggle="modal" data-target="#deleteTrailerType">
                    <span>Delete options</span>
                </a>

                <button type="submit" class="btn btn-block btn-success">Insert</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
