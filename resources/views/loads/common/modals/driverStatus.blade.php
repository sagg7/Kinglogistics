<div class="modal fade" id="driverStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="dropdown float-right">
                <button class="btn pr-0 waves-effect waves-light float-right" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                    <a class="dropdown-item" id="completeAll" onclick="downloadShift()"><i class="fas fa-file-excel"></i> Download Dispatch Report</a>
                </div>
            </div>
            <div class="modal-body mr-2 ml-2">
                <div class="aggrid ag-theme-material w-100 mb-5" id="driverStatusTable"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
