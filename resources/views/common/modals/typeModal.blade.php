<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New {{ $name }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => $route, 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'data-target-select' => '#' . $selectId]) !!}

                <div class="form-content">

                    <div class="form-group">
                        <label>Name</label>
                        <div class="input-group">
                            <input id="name" class="form-control" name="name" type="text">
                        </div>
                    </div>

                </div>

                <a class="d-block text-muted float-right mb-2" href="#{{ $deleteTypeModalId }}" data-dismiss="modal" data-toggle="modal" data-target="#{{ $deleteTypeModalId }}">
                    <span>Delete options</span>
                </a>

                <button type="submit" class="btn btn-block btn-success">Insert</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
