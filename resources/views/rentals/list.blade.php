@extends('layouts.app')

@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--6" bis_skin_checked="1">
        <div class="row justify-content-center" bis_skin_checked="1">
            <div class=" col " bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header bg-transparent" bis_skin_checked="1">
                        <h3 class="mb-0">Rentals</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Leased Name</th>
                                <th scope="col">Trailer #</th>
                                <th scope="col">Driver Name</th>
                                <th scope="col">Rental date</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody class="table-striped" id="rentedTable">
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.footers.auth')
    @push('js')
    <script>
        function initTable(){
            $.ajax({
                'url': '{{url('getRented')}}/',
                'type': 'get',
                'data': {'search': $("#search").val()},
                'dataType': 'json',
                'success': function(data){
                    fillTable(data, true);
                }
            });
        }

        function fillTable(data, empty) {
            let table = $("#rentedTable");
            if (empty)
                table.html("");
            let content = "";
            let url = "{{url("inspection/create")}}";
            let urlend = "{{url("endInspection/create")}}";
            for (let i = 0; i < data.data.length; i++){
                let color = '';
                let button = '';
                if (data.data[i].rental_status == 'Uninspected'){
                    color = "blue";
                    button = `<a class="dropdown-item" href="${url}/${data.data[i].id}"><i class="fas fa-clipboard-list"></i></i><span>create Inspection</span></a>`;
                }
                if (data.data[i].rental_status == 'Rented'){
                    color = "green";
                    button = `<a class="dropdown-item" href="${urlend}/${data.data[i].id}"><i class="fas fa-window-close"></i><span>End Rental</span></a>`;
                }
                content += `<tr>`+
                    `<th scope="row">`+
                        `<div class="status bg-${color}" bis_skin_checked="1"></div>`+
                    `</th>`+
                    `<th scope="row">`+
                    `    <div class="media align-items-center">`+
                    `            <span class="mb-0 text-sm">${data.data[i].leased_name}</span>`+
                    `    </div>`+
                    `</th>`+
                    `    <td>${data.data[i].trailer_number}</td>`+
                    `    <td>${data.data[i].driver_name}</td>`+
                    `    <td>${data.data[i].rental_date}</td>`+
                    `    <td class="text-right">`+
                    `    <div class="dropdown">`+
                    `        <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">`+
                    `            <i class="fas fa-ellipsis-v"></i>`+
                    `        </a>`+
                    `        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">`+
                    `            <a class="dropdown-item" href="#"><i class="fas fa-file-download"></i></i><span>Create Pdf</span></a>`+
                                 button+
                    `        </div>`+
                    `    </div>`+
                    `</td>`;
            }
            table.html(content);
        }
        $(document).ready(function () {
            initTable();
            $("#search").change(function (e){
                initTable();
            });
        })
    </script>
    @endpush

@endsection
