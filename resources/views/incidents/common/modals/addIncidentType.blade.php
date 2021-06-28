<div class="modal fade" id="addIncidentType" tabindex="-1" role="dialog" aria-labelledby="incident-type-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="incident-type-modal-label">New Incident Type</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                {!! Form::open(['route' => 'incidentType.store', 'method' => 'post', 'class' => 'form form-vertical optionHandler', 'data-handler-action' => 'create', 'data-target-select' => '#incident_type_id']) !!}

                <div class="form-content">

                    <div class="form-group">
                        <label>Name</label>
                        <div class="input-group">
                            <input id="name" class="form-control" name="name" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fine</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            <input id="fine" class="form-control" name="fine" type="text">
                        </div>
                    </div>

                </div>

                <a class="d-block text-muted float-right mb-2" href="#deleteIncidentType" data-dismiss="modal" data-toggle="modal" data-target="#deleteIncidentType">
                    <span>Delete options</span>
                </a>

                <button type="submit" class="btn btn-block btn-success">Insert</button>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>
