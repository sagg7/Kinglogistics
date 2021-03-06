<x-pdf-layout>
        <script>
            //window.onload = function() {
            //    console.log("eeeee");
            //    setTimeout("window.print();", 1050);
            //    setTimeout("window.history.back()",1051);
            //}
        </script>
    @foreach($loads as $load)
        <div style="height: 220mm">
            <div>
                <p style="text-align:center; border-style: solid; border-radius: 50px">
                @if($load['inspected'])
                    <span>&#128994;</span>
                @else
                    <span>&#128993;</span>
                @endif
                    Driver Name - <strong>{{$load['driverName']}}</strong>, {{session('renames')->job ?? 'Job'}} - <strong>{{$load['job']}}</strong>, {{session('renames')->control_number ?? 'Control number'}} - <strong>{{$load['control_number']}}</strong>,         Customer Reference - <strong>{{$load['customer_reference']}}</strong>,     "{{session('renames')->bol ?? 'BOL'}}" - <strong>{{$load['bol']}}</strong>,     Finished Timestamp - <strong>{{$load['finished_timestamp']}}</strong>,     Status - <strong>{{$load['status']}}</strong>
                    </p>
            </div>
            <div style="width:50%; float: left;">
                <img src="{{$load['ticket']}}" style="width:100%; height:auto; display: block; page-break-after:always; max-height: 200mm;">
            </div>
            <div style="width:50%; float: right;">
                <img src="{{$load['finished']}}" style="width:100%; height:auto; display: block; page-break-after:always; max-height: 200mm;">
            </div>
        </div>

    @endforeach
</x-pdf-layout>

