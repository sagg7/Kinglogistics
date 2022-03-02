function fillFormDispatchReport() {
    let formDispatchReport = $("#createDispatchReportModal")
    $.ajax({
        type: 'GET',
        url: '/report/dailyDispatchReport',
        data: {
            //'shipper_id' : $('#shipper').val()
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
        },
        success: (res) => {
            const customerForm = formDispatchReport.find("#definidor");

            customerForm.empty();

            customerForm.append(
                `<div class="form-group">` +
                `<div class="row">` +
                `<div class="col-md-3">Dispatch Name:</div>` +
                ` <div class="col-md-3">${res.name}</div>` +
                `<div class="col-md-3">Date:</div>` +
                `<div class="col-md-3">${res.date}</div>` +
                `</div><br>` +
                `<div class="row">` +
                `<div class="col-md-3">Hours:</div>` +
                `<div class="col-md-3">${res.hours}</div>` +
                `<div class="col-md-3">In Transit:</div>` +
                `<div class="col-md-3">${res.active_loads}</div>` +
                `</div><br>` +
                `<div class="row">` +
                `<div class="col-md-3">Active Trucks:</div>` +
                `<div class="col-md-3">${res.active_drivers}</div>` +
                `<div class="col-md-3">Inactive Trucks:</div>` +
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
                `<div for="situationDescription">Situation Description: </div>` +
                `<textarea id="situationDescription" name="situationDescription" rows="4" placeholder="Was there an event?" style="width:100%"></textarea><br><br>` +
                `<div for="situationDescription">Well Status: </div>` +
                `<textarea id="wellStatus" name="wellStatus" rows="4" style="width:100%" placeholder="Describe Well Status:"></textarea>` +
                `</div>`
            );
        },
        error: () => {
            throwErrorMsg();
        }
    });
    $("#createDispatchReportModal").modal('show');
}


(() => {
    $('#submitDispatchReport').submit(function(e){
        e.preventDefault();
        let modal = $('#createDispatchReportModal'),
            form = modal.find("textarea").serialize();
        $.ajax({
            type: 'POST',
            url: '/report/storeDispatchReport',
            data: form,
            success: (res) => {
                throwErrorMsg("Report Generated correctly", {"title": "Success!", "type": "success"});
                $('#createDispatchReportModal').modal('hide');
                $.each(modal.find("textarea"), function( index, value ) {
                    value.value = "";
                  });
            },
            error: () => {
                throwErrorMsg();
            }
        })
    });

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
