<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title">{{$title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="dropdown float-right">
                <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                    <a class="dropdown-item" id="downloadTmpXLS" href="/load/downloadTmpXLS"><i class="fas fa-file-excel"></i> Download Excel Template</a>
                </div>
            </div>
            <BR>
            @isset($template)
            <div class="dropdown float-right">
                <button class="btn mb-1 pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end" style="display: none">
                    <a class="dropdown-item" id="downloadTmpXLS" href="{{!!$template!!}}"><i class="fas fa-file-excel"></i> Download Excel Template</a>
                </div>
            </div>
            @endisset
            <BR>
            <div class="modal-body">
                {!! Form::open(['route' => $route, 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data', 'id' => $idForm ]) !!}
                <div class="file-group" style="margin-bottom: 20px">
                    <label for="fileExcel" class="btn form-control btn-block bg-warning"  style="height: 200px; width: 200px; border-radius: 50%; margin: auto;">
                        <i class="fas fa-file-upload fa-5x" style="color: white; margin-top: 50px"></i>
                        <input type="file" name="fileExcel" id="fileExcel" hidden accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    </label>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-file d-none"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning btn-block mr-1 mb-1 waves-effect waves-light submit-ajax text-white" disabled>Upload</button>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>