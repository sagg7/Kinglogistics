<div class="modal fade" id="deleteLoadType" tabindex="-1" role="dialog" aria-labelledby="load-type-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="load-type-modal-label">Delete Load Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'loadType.delete', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'delete', 'data-target-select' => '#load_type_id']) !!}

                    <div class="form-group">
                        <select id="delete_type" class="form-control" name="id"></select>
                    </div>

                    <button type="submit" class="btn btn-block btn-danger">Delete</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
