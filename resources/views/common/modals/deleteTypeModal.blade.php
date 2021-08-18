<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="incident-type-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="type-modal-label">Delete {{ $name }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => $route, 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'delete', 'data-target-select' => '#' . $selectId]) !!}

                <div class="form-group">
                    <select id="delete_type" class="form-control" name="id"></select>
                </div>

                <button type="submit" class="btn btn-block btn-danger">Delete</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
