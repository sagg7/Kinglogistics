<div class="modal fade" id="deleteRateGroup" tabindex="-1" role="dialog" aria-labelledby="rate-group-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="rate-group-modal-label">Delete Rate Group</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'rateGroup.delete', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'delete', 'data-target-select' => '#rate_group']) !!}

                    <div class="form-group">
                        <select id="delete_type" class="form-control" name="id"></select>
                    </div>

                    <button type="submit" class="btn btn-block btn-danger">Delete</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
