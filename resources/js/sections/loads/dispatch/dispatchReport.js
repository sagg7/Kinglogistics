function getDispatchReport() {
    $.ajax({
        type: 'GET',
        url: '/report/getDispatchReport',
        data: {
        //   'shipper_id' : $('#shipper').val()
        },
        success: (res) => {
            let showDispatchReports =  $("#DispatchReportModal");
            showDispatchReports.modal('show');
            const customerForm = showDispatchReports.find('tbody');

            customerForm.empty();
        for(var i=0; i < res.length; i++)
        {

            customerForm.append(
                `<tr id="${res[i].id}" class="reports"><td>${res[i].dispatch.name}</td>` +
                `<td>${res[i].date}</td>` +
              `</tr>` );
        }

        $('.reports').click(function(){

            let id = $(this).attr("id");
            let modal = $('#DispatchReportModal')
            //     form = modal.find("textarea").serialize();
            $.ajax({
                type: 'GET',
                url: '/report/showDispatchReportById/'+id,
                data:{
                'name': $("params.name").val(),
                'date': $("params.date").val(),
                'hours': $("params.hours").val(),
                'active_loads': $("params.active_loads").val(),
                'active_drivers':$("params.active_drivers").val(),
                'inactive_drivers':$("params.inactive_drivers").val(),
                'loads_finalized':$("params.loads_finalized").val(),
                'worked_time':$("params.worked_time").val(),
                'dispatch_score':$("params.dispatch_score").val(),
                'score_app_usage':$("params.score_app_usage").val(),
                'well_status':$("params.well_status").val(),
                'description':$("params.description").val(),
            } ,

                success: (res) => {
                    let showDispatchReport =  $("#showDispatchReport");
                    showDispatchReport.modal('show');
                    const bodyOfDistpatchReport = showDispatchReport.find('.modal-body');
                    bodyOfDistpatchReport.empty();
                    bodyOfDistpatchReport.empty();
                    bodyOfDistpatchReport.append(
                        `<div class="form-group">` +
                        `<div class="row">` +
                        `<div class="col-md-3">Dispatch Name:</div>` +
                        ` <div class="col-md-3">${res.name}</div>` +
                        `<div class="col-md-3">Date:</div>` +
                        `<div class="col-md-3">${res.date}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">Hour:</div>` +
                        `<div class="col-md-3">${res.hours}</div>` +
                        `<div class="col-md-3">In Transit:</div>` +
                        `<div class="col-md-3">${res.active_loads}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">Active Truck:</div>` +
                        `<div class="col-md-3">${res.active_drivers}</div>` +
                        `<div class="col-md-3">Inactive Truck:</div>` +
                        `<div class="col-md-3">${res.inactive_drivers}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">Time in Dispatch:</div>` +
                        `<div class="col-md-3">${msToTime(res.worked_time*1000*60, false)}</div>` +
                        `<div class="col-md-3">Loads Dispatch:</div>` +
                        `<div class="col-md-3">${res.loads_finalized}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3">App Usage Score</div>` +
                        `<div class="col-md-3">${res.score_app_usage}</div>` +
                        `<div class="col-md-3">Dispatch Score</div>` +
                        `<div class="col-md-3">${res.dispatch_score}</div>` +
                        `</div><br>` +
                        `<div class="row">` +
                        `<div class="col-md-3"><p>Well Status:</p></div>` +
                        `<div class="col-md-3"><p>${res.well_status}</p></div>` +
                        `<div class="col-md-3"><p>Description:</p></div>` +
                        `<div class="col-md-3"><p>${res.description}</p></div>` +
                        `</div><br>`
                    )
                },
                error: () => {
                    throwErrorMsg();
                }
            })
        });
        },
        error: () => {
            throwErrorMsg();
        }
    });
}


(() => {


    // getDispatchReport($('#DispatchReportModal'));

    const nameFormatter = (params) => {
        if (params.value)
            return params.value.name;
        else
            return '';
    };
    const carrierPhoneFormatter = (params) => {
        if (params.data && params.data.carrier.phone)
            return params.data.carrier.phone;
        else
            return '';
    };
    const trailerNumberFormatter = (params) => {
        if (params.data && params.data.truck && params.data.truck.trailer)
            return params.data.truck.trailer.number;
        else
            return '';
    };

})();
