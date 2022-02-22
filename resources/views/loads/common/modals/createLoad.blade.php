<div class="modal fade" id="createLoadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="load-type-modal-label">Create Load</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'load.store', 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'loadForm']) !!}
                @include('loads.common.form', ['ajax' => true])
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
