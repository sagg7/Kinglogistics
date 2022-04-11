<div class="modal fade" id="optionToSelectTruck" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $modalClass ?? 'modal-lg' }}"
        role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title">Do You Want Add A Truck?</h5>
                <button type="button" class="close" id="optionToSelectTruckNo" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row text-center" style="display: flex;align-items: center;justify-content: center;">
                    <button type="button" class="btn btn-success btn-block col-md-3" id="optionToSelectTruckYes">Yes</button>
                    <br>
                    <button type="button" class="btn btn-danger btn-block col-md-3" id="optionToSelectTruckNo" data-dismiss="modal">No</button>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
