<x-pdf-layout>
    @foreach($photos as $photo)
    <div>
        <img src="{{$photo}}" style="width:100%; height:auto; display: block; page-break-after:always; max-height: 290mm;">
    </div>
    @endforeach
</x-pdf-layout>
