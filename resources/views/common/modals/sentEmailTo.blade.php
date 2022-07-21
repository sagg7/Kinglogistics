<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $title }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => $route, 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'data-target-select' => '#' . $selectId, 'id' => $selectId ]) !!}

                <div class="form-content">

                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <input id="email" class="form-control" name="email" type="text">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-md-6 text-center"><a id="BottomContent" onclick="getLink()">Get Link</a></div>
                    <div class="form-group col-md-6 text-center" id="getLink"></div>
                </div>
                <button type="submit" class="btn btn-block btn-success">Sent</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
