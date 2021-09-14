<section id="loads-summary">
    <div class="card bg-analytics">
        <div class="card-content">
            <div class="card-body text-center">
                <div class="avatar avatar-lg bg-rgba-info p-50 m-0 mb-1">
                    <div class="avatar-content text-white">
                        <i class="fas fa-truck-loading font-large-1"></i>
                    </div>
                </div>
                <div class="text-center">
                    <h2 class="mb-2">Loads Status</h2>
                </div>
                <div class="row text-left">
                    <div class="col col-sm-6">
                        <fieldset class="form-group">
                            <label for="trips">Job</label>
                            {!! Form::select('trips', [], null, ['class' => 'form-control']) !!}
                        </fieldset>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th><h6>Unallocated</h6></th>
                            <th><h6>Requested</h6></th>
                            <th><h6>Accepted</h6></th>
                            <th><h6>Loading</h6></th>
                            <th><h6>In&nbsp;transit</h6></th>
                            <th><h6>Arrived</h6></th>
                            <th><h6>Unloading</h6></th>
                            <th><h6>Finished</h6></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><a href="#"><h2 id="unallocated_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="requested_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="accepted_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="loading_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="to_location_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="arrived_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="unloading_summary">0</h2></a></td>
                            <td><a href="#"><h2 id="finished_summary">0</h2></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
