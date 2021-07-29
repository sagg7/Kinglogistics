<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="row">

                <div class="col pl-0 pr-0" style="max-width: 200px">
                    <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                        @foreach($pills as $i => $pill)
                            <li class="nav-item">
                                <a class="nav-link d-flex py-75 {{ $i == 0 ? 'active' : '' }}" data-toggle="pill" href="#{{ $pill['pane'] }}"
                                   aria-expanded="true">
                                    @isset($pill['icon'])<i class="{{ $pill['icon'] }} mr-50 font-medium-3"></i>@endisset
                                    {{ $pill['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col">
                    <div class="tab-content">
                        {{ $slot }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
