<div class="modal fade" id="carrier-data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Carrier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center p-5 modal-spinner">
                    <span class="spinner-border" role="status" aria-hidden="true"></span>
                </div>
                <div class="content-body d-none">
                    <table class="table" id="carrier-info">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr></tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <h3>Active drivers</h3>
                        <hr>
                        <div id="activeCarrierDrivers" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
                    </div>
                    <div class="form-group">
                        <h3>Inactive drivers</h3>
                        <hr>
                        <div id="inactiveCarrierDrivers" class="aggrid ag-auto-height total-row ag-theme-material w-100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
