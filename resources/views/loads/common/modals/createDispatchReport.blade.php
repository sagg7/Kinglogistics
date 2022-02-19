<div class="modal fade" id="createDispatchReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Report Dispatch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                            
                {!! Form::open(['route' => 'report.storeDispatchReport', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 
                'data-handler-action' => 'create', "id" => "submitDispatchReport"]) !!}
             
                <div class="form-content">
                    
                    <div id="definidor">

                    </div>
                </div>

                <button type="submit" class="btn btn-block btn-success">Insert</button>

                {!! Form::close() !!}

            </div>

        </div>
    </div>
</div>

{{-- Este iba adentro de la linea del form 
, 'data-target-select' => '#load_type_id' --}}