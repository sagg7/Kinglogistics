(() => {

})();

$('#formRoadLoads').submit(e => {
    
    // console.log('haol');
    e.preventDefault();
    const form = $(e.currentTarget);
    const formData = new FormData(form[0]);

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: (res) => {
            if (res.success){
            throwErrorMsg("Load Generated Successfully", {"title": "Success!", "type": "success"});
            $("#postLoadModal").modal('hide');
            }
        },
        error: () => {
            throwErrorMsg();
        }
    })
});

   const loadType = $('#load_type_id');
        loadType.select2({
        placeholder: 'Select',
        ajax: {
            url: '/roadLoads/selectionLoadType',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
                console.log(params);
            },
        },
    });

   const trailerType = $('#trailer_type_id');
        trailerType.select2({
        placeholder: 'Select',
        ajax: {
            url: '/roadLoads/selectionTrailerType',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
                console.log(params);
            },
        },
    });

   const loadMode = $('#mode_id');
        loadMode.select2({
        placeholder: 'Select',
        ajax: {
            url: '/roadLoads/selectionLoadMode',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
                console.log(params);
            },
        },
    });

   const statesOrigin = $('#statesOrigin');
        statesOrigin.select2({
        placeholder: 'Select',
        ajax: {
            url: '/roadLoads/selectionStates',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
                console.log(params);
            },
        },
    });
    

    const citiesOrigin = $('#citiesOrigin');
    citiesOrigin.select2({
         placeholder: 'Select',
         ajax: {
             url: '/roadLoads/selectionCity/',
             data: (params) => {
                 return {
                     search: params.term,
                     page: params.page || 1,
                     take: 15,
                     statesOrigin:statesOrigin.val(),
                 };
                 console.log(params);
             },
         },
     });

    const stateDestination = $('#stateDestination');
         stateDestination.select2({
         placeholder: 'Select',
         ajax: {
             url: '/roadLoads/selectionStates',
             data: (params) => {
                 return {
                     search: params.term,
                     page: params.page || 1,
                     take: 15,
                 };
                 console.log(params);
             },
         },
     });

    
    const cityDestination = $('#cityDestination');
    cityDestination.select2({
         placeholder: 'Select',
         ajax: {
             url: '/roadLoads/selectionCity/',
             data: (params) => {
                 return {
                     search: params.term,
                     page: params.page || 1,
                     take: 15,
                     stateDestination:stateDestination.val(),
                 };
                 console.log(params);
             },
         },
     });

    $('#origin_early_pick_up_date').daterangepicker({
        "singleDatePicker": true,
        "timePicker": true,
        "timePicker24Hour": true,
        "startDate": moment().startOf('hour'),
        "endDate": moment().startOf('hour').add(32, 'hour'),
        "locale": {
            format: 'YYYY/MM/DD hh:mm:ss'
          }

    }, function(start, end, label) {
    //   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    $('#origin_early_pick_up_date').daterangepicker({
        "singleDatePicker": true,
        "timePicker": true,
        "timePicker24Hour": true,
        "startDate": moment().startOf('hour'),
        "endDate": moment().startOf('hour').add(32, 'hour'),
        "locale": {
            format: 'YYYY/MM/DD hh:mm:ss'
          }

    }, function(start, end, label) {
    //   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
    $('#origin_late_pick_up_date').daterangepicker({
        "singleDatePicker": true,
        "timePicker": true,
        "timePicker24Hour": true,
        "startDate": moment().startOf('hour'),
        "endDate": moment().startOf('hour').add(32, 'hour'),
        "locale": {
            format: 'YYYY/MM/DD hh:mm:ss'
          }

    }, function(start, end, label) {
    //   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
    $('#destination_early_pick_up_date').daterangepicker({
        "singleDatePicker": true,
        "timePicker": true,
        "timePicker24Hour": true,
        "startDate": moment().startOf('hour'),
        "endDate": moment().startOf('hour').add(32, 'hour'),
        "locale": {
            format: 'YYYY/MM/DD hh:mm:ss'
          }

    }, function(start, end, label) {
    //   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
    $('#destination_late_pick_up_date').daterangepicker({
        "singleDatePicker": true,
        "timePicker": true,
        "timePicker24Hour": true,
        "startDate": moment().startOf('hour'),
        "endDate": moment().startOf('hour').add(32, 'hour'),
        "locale": {
            format: 'YYYY/MM/DD hh:mm:ss'
          }

    }, function(start, end, label) {
    //   console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });



    statesOrigin.change(function() {
        citiesOrigin.html(`<option value=""></option>`);
    });
    stateDestination.change(function() {
        cityDestination.html(`<option value=""></option>`);
      });
