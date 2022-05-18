<section class="row align-items-center" style="min-height: calc(100vh - 125px)">
    <div class="col text-center">
        <h1 class="fa-solid fa-road-barrier" style="font-size: 12em; color: #fcce3a;"></h1>
        <h1 class="font-large-2 my-1">{{ $title }}</h1>
        <p class="p-2">
            {{ $text }}
        </p>
        @isset($btns)
            @foreach($btns as $item)
                <a class="btn btn-primary btn-lg mt-2 waves-effect waves-light" href="{{ $item["href"] }}" @isset($item['id'])id="{{ $item['id'] }}"@endisset>{{ $item["btn_text"] }}</a>
            @endforeach
        @elseif($href)
            <a class="btn btn-primary btn-lg mt-2 waves-effect waves-light" href="{{ $href }}">{{ $btn_text }}</a>
        @endisset
    </div>
</section>
