<x-pdf-layout>
        <script>
            window.onload = function() {
                console.log("eeeee");
                setTimeout("window.print();", 1050);
                setTimeout("window.history.back()",1051);
            }
        </script>
    @foreach($photos as $photo)
    <div>
        <img src="{{$photo}}" style="width:100%; height:auto; display: block; page-break-after:always; max-height: 250mm;">
    </div>
    @endforeach
</x-pdf-layout>

