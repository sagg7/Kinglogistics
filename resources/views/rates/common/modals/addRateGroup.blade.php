<div class="modal fade" id="addRateGroup" tabindex="-1" role="dialog" aria-labelledby="rate-group-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="rate-group-modal-label">New Rate Group</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'rateGroup.store', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'data-target-select' => '#rate_group']) !!}

                <div class="form-content">

                    <div class="form-group">
                        <label>Name</label>
                        <input id="name" class="form-control" name="name" type="text">
                    </div>

                </div>

                <a class="d-block text-muted float-right mb-2" href="#deleteRateGroup" data-dismiss="modal" data-toggle="modal" data-target="#deleteRateGroup">
                    <span>Delete options</span>
                </a>

                <button type="submit" class="btn btn-block btn-success">Insert</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
