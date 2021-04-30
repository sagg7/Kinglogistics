@extends('layouts.app')

@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--6" bis_skin_checked="1">
        <div class="row justify-content-center" bis_skin_checked="1">
            <div class=" col " bis_skin_checked="1">
                <div class="card" bis_skin_checked="1">
                    <div class="card-header bg-transparent" bis_skin_checked="1">
                        <h3 class="mb-0">Trailers</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Trailer Number</th>
                                <th scope="col">Trailer type</th>
                                <th scope="col">Status</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody class="table-striped" id="leasedTable">
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
                'url': '{{url('getTrailers')}}/',
                'type': 'get',
                'data': {'search': $("#search").val()},
                'dataType': 'json',
                'success': function(data){
                    fillTable(data, true);
                }
            });
        }

        function fillTable(data, empty) {
            let table = $("#leasedTable");
            if (empty)
                table.html("");
            let content = "";
            let edit = "{{url("trailer/edit")}}";
            let urlRemove = "{{url("trailer/remove")}}";
            for (let i = 0; i < data.data.length; i++){
                let color = "green";
                if (data.data[i].status == "rented")
                    color = "";

                content += `<tr>`+
                    `<th scope="row">`+
                    `<div class="status bg-${color}"  data-toggle="tooltip" data-placement="top" title="${data.data[i].status}" bis_skin_checked="1"></div>`+
                    `</th>`+
                    `<th scope="row">`+
                    `    <div class="media align-items-center"">`+
                    `            <span class="mb-0 text-sm">${data.data[i].trailer_number}</span>`+
                    `    </div>`+
                    `</th>`+
                    `    <td>${data.data[i].name}</td>`+
                    `    <td>${data.data[i].status}</td>`+
                    `    <td class="text-right">`+
                    `    <div class="dropdown">`+
                    `        <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">`+
                    `            <i class="fas fa-ellipsis-v"></i>`+
                    `        </a>`+
                    `        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">`+
                    `            <a class="dropdown-item" href="${edit}/${data.data[i].id}"><i class="far fa-eye"></i></i><span>Edit</span></a>`+
                    `            <li role="separator" class="divider"></li>`+
                    `            <a class="dropdown-item" href="${urlRemove}/${data.data[i].id}"><i class="fas fa-user-times"></i><span>delete</span></a>`+
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
