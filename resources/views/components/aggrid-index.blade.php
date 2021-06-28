<div class="card">
    <div class="card-content">
        @isset($create_btn)
            <div class="card-header">
                <a href="{{ $create_btn['url'] }}" class="btn btn-primary">{{ $create_btn['text'] }}</a>
            </div>
            <hr>
        @endisset
        <div class="card-body">
            <div id="myGrid"></div>
        </div>
    </div>
</div>
