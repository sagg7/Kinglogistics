<div class="modal transparent" tabindex="-1" id="swiperModal">
    <div class="modal-dialog modal-lg transparent-modal modal-slider" style="margin: 0 auto">
        <div class="modal-content">
            <div class="">
                <div class="center-screen">
                    <button id="x" data-toggle="modal" data-target="#swiperModal" modal="dismiss">X</button>
                    <div class="owl-carousel owl-theme" id='picturesCarousel'>
                        @foreach ($pictures->sortBy('picture_id')->reverse() as $index => $picture)
                            <div id="slider-{{$picture->id}}">
                                <img class="owl-lazy" data-src="{{$picture->picture_path.$picture->picture_slider}}"
                                     alt="" title="{{$picture->picture_description}}">
                                <div class='context' style="background-color:white; text: black;">
                                    <p>{{$picture->picture_description}}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
