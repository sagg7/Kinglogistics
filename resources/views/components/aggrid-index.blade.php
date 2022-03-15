<div class="card">
    <div class="card-content">
        @if(isset($create_btn) || isset($menu))
            <div class="card-header align-items-center">
                <div class="col-8">
                    @isset($create_btn)
                        <a href="{{ $create_btn['url'] }}" class="btn btn-primary">{{ $create_btn['text'] }}</a>
                    @endisset
                </div>
                @isset($menu)
                    <div class="col-4">
                        <div class="dropdown float-right">
                            <button class="btn pr-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                @foreach($menu as $anchor)
                                    <a href="{{ $anchor['url'] }}" class="dropdown-item" @isset($anchor['attributes']) @foreach($anchor['attributes'] as $name => $attribute){{ "$name=$attribute " }}@endforeach @endisset><i class="{{ $anchor['icon'] ?? '' }}"></i> {{ $anchor['text'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <hr>
        @endif
        <div class="card-body">
            <div id="myGrid"></div>
        </div>
    </div>
</div>
