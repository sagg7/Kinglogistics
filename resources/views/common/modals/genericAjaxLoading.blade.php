<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title ?? null }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @isset($content)
                    <div class="content-body">{!! $content !!}</div>
                @else
                    <div class="text-center p-5 modal-spinner">
                        <span class="spinner-border" role="status" aria-hidden="true"></span>
                    </div>
                    <div class="content-body d-none"></div>
                @endisset
            </div>
            <div class="modal-footer">
            @isset($footerButton)
                {!! $footerButton !!}
            @else
                <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
            @endisset
            </div>

        </div>
    </div>
</div>
