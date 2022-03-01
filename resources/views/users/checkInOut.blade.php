<x-app-layout>
    <x-slot name="crumb_section">Staff</x-slot>
    <x-slot name="crumb_subsection">Spotter Check In/Out</x-slot>
    
    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "getCheckInModal", "title" => "Make Check In"])
        @include("common.modals.genericAjaxLoading", ["id" => "getCheckOutModal", "title" => "Make Check Out"])     
    @endsection
    @section("head")

    {{-- <link href="{{ asset('public\app-assets\css\bootstrap-extended.css') }}" rel="stylesheet"> --}}
    @endsection
  
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        {{-- {{$checkTime[0]) ? $checkTime[0]->id :  0 }} --}}
                        {{-- @dd($checkTime) --}}
                        {{-- @if($checkTime) --}}
                        <button id="checkOutId" type="button" class="btn btn-primary" onclick="getCheckOutModal()">Make Check Out</button>
                        {{-- @else --}}
                        <button id="checkInId" type="button" class="btn btn-primary"  onclick="getCheckInModal()">Make Check In</button>
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
  
    
    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script defer>
            var tbAG = null;
         let checkId = "{{($checkTime ? $checkTime->id : 0)}}";
            (() => {
                function CoordsLinkRenderer() {}
                CoordsLinkRenderer.prototype.init = (params) => {
                    this.eGui = document.createElement('div');
                    // const coords = params.value;
                    // const arr = coords.split(',');
                    // const latitude = Number(arr[0]).toFixed(5);
                    // const longitude = Number(arr[1]).toFixed(5);
                    this.eGui.innerHTML = `<a href="http://www.google.com/maps/place/${params.value},${params.data.longitude_check_in}" target="_blank">${params.data.latitude_check_in},${params.data.longitude_check_in}</a>`;
                    this.eGui.innerHTML = `<a href="http://www.google.com/maps/place/${params.value},${params.data.longitude_check_out}" target="_blank">${params.data.latitude_check_out},${params.data.longitude_check_out}</a>`;
                }
                CoordsLinkRenderer.prototype.getGui = () => {
                    return this.eGui;
                }

                let userName = (params) => {
                    if (params.value)
                        return params.value.name;
                };
                //Format to get time and date sort
                let checkInTime = (params) => {
                    if (params.value){
                        var res = new Date(params.data.check_in);
                    let hours = res.getHours();
                    let minutes = "0" + res.getMinutes();
                    let seconds = "0" + res.getSeconds();
                    let formattedTime = hours + ':' + minutes.substr(-2)   + ' '+ (res.getMonth()+1)+"/"+res.getDate()+"/"+res.getFullYear();
                        return (formattedTime);
                    }
                  
                };
                let checkOutTime = (params) => {
                    if (params.value){
                        var res = new Date(params.data.check_out);
                    let hours = res.getHours();
                    let minutes = "0" + res.getMinutes();
                    let seconds = "0" + res.getSeconds();
                    let formattedTime = hours + ':' + minutes.substr(-2)  + ' '+ (res.getMonth()+1)+"/"+res.getDate()+"/"+res.getFullYear();
                        return (formattedTime);
                    }
                  
                };
                
                //This is the seconder
            function addTime() {
                $(".update").each(function (index) {
                    let time = parseFloat($(this).attr('time')) + 1000;
                    $(this).html(msToTime(time));
                    $(this).attr('time', time);
                });
                setTimeout(() => {
                    addTime();
                }, 1000);
            }
            //function to format the time 
                function msToTime(duration, showSeconds = true) {
                let seconds = Math.floor((duration / (1000)) % 60),
                    minutes = Math.floor((duration / (1000 * 60)) % 60),
                    hours = Math.floor((duration / (1000 * 60 * 60)) % 24),
                    days = Math.floor((duration / (1000 * 60 * 60 * 24)));

                hours = (hours < 10) ? "0" + hours : hours;
                minutes = (minutes < 10) ? "0" + minutes : minutes;
                let secs = "";
                if (showSeconds)
                    secs = seconds + " s";
                if (days > 0)
                    return days + " d " + hours + " h " + minutes + " m " + secs;
                else if (hours > 0)
                    return hours + " h " + minutes + " m " + secs;
                else
                    return minutes + " m " + secs;
            }

                let msToTimeLet = (params) => {
                    if(params.value)
                    var timeFormatted = msToTime(params.data.worked_hours*60*1000, false) ;
                    return timeFormatted;
                }

                tbAG = new tableAG({
                    columns: [
                        {headerName: 'Name', field: 'user', valueFormatter: userName},
                        {headerName: 'Check In Geolocation', field: 'latitude_check_in', cellRenderer: CoordsLinkRenderer},
                        {headerName: 'Check Out Geolocation', field: 'latitude_check_out', cellRenderer: CoordsLinkRenderer},
                        {headerName: 'Check In', field: 'check_in', valueFormatter: checkInTime},
                        {headerName: 'Check Out', field: 'check_out', valueFormatter: checkOutTime},
                        {headerName: 'Worked Time', field: 'worked_hours', valueFormatter: msToTimeLet},
                    ],
                    // menu: [
                    //     @if(auth()->user()->can(['update-staff']))
                    //     {text: 'Edit', route: '/user/edit', icon: 'feather icon-edit'},
                    //     @endif
                    //     @if(auth()->user()->can(['delete-staff']))
                    //     {route: '/user/delete', type: 'delete'}
                    //     @endif
                    // ],
                    container: 'myGrid',
                    url: '/user/searchCheckInOut',
                    tableRef: 'tbAG',
                    successCallback: (res) => {
                        /*res.rows.forEach((item) => {
                            item.area = item.name;
                        });*/
                    }

                });
            })();
        </script>
          <script src="{{ asset('js/sections/users/checkInOut.min.js') }}"></script>
          {{-- <script src="{{ asset('js/sections/users/checkInOut.js') }}"></script> --}}
    @endsection

    <x-aggrid-index></x-aggrid-index>
</x-app-layout>
