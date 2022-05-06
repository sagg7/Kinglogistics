(() => {
    const loadType = $('#load_type_id');
    const trailerType = $('#trailer_type_id');
    const loadMode = $('#mode_id');
    const stateOrigin = $('#statesOrigin');
    const cityOrigin = $('#citiesOrigin');
    const stateDestination = $('#stateDestination');
    const cityDestination = $('#cityDestination');
    const shipperSel = $('#shipper');

    const clearForm = () => {
        stateOrigin.html(`<option value=""></option>`);
        cityOrigin.html(`<option value=""></option>`);
        stateDestination.html(`<option value=""></option>`);
        cityDestination.html(`<option value=""></option>`);
        loadType.html(`<option value=""></option>`);
        trailerType.html(`<option value=""></option>`);
        loadMode.html(`<option value=""></option>`);
        $('#origin_early_pick_up_date').val("");
        $('#origin_late_pick_up_date').val("");
        $('#destination_early_pick_up_date').val("");
        $('#destination_late_pick_up_date').val("");
        $('#shipper_rate').val("");
        $('#rate').val("");
        $('#weight').val("");
        $('#tons').val("");
        $('#width').val("");
        $('#height').val("");
        $('#length').val("");
        $('#pieces').val("");
        $('#pallets').val("");
        $('#mileage').val("");
        $('#silo_number').val("");
        $('#customer_po').val("");
        $('#control_number').val("");
        $('#pay_rate').val("");
        $('#load_size').val("");
        $('#days_to_pay').val("");
        $('#notes').val("");
    };

    $('#formRoadLoads').submit(e => {
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
                    clearForm();
                } else {
                    throwErrorMsg();
                }
            },
            error: (res) => {
                console.log(res);
                let errors = `<ul class="text-left">`;
                Object.values(res.responseJSON.errors).forEach((error) => {
                    errors += `<li>${error}</li>`;
                });
                errors += `</ul>`;
                throwErrorMsg(errors, {timer: false});
            },
        })
    });

    loadType.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionLoadType',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    });
    trailerType.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionTrailerType',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    });
    loadMode.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionLoadMode',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    });
    stateOrigin.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionStates',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    });
    cityOrigin.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionCity/',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    statesOrigin:stateOrigin.val(),
                };
            },
        },
    });
    stateDestination.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionStates',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    });
    cityDestination.select2({
        placeholder: 'Select',
        ajax: {
            url: '/load/road/selectionCity/',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    stateDestination:stateDestination.val(),
                };
            },
        },
    });
    if (shipperSel.length > 0) {
        shipperSel.select2({
            ajax: {
                url: '/shipper/selection',
                data: (params) => {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        take: 15,
                    };
                },
            },
            placeholder: 'Select',
            allowClear: true,
        });
    }

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

    stateOrigin.change(function() {
        cityOrigin.html(`<option value=""></option>`);
    });
    stateDestination.change(function() {
        cityDestination.html(`<option value=""></option>`);
    });
})();
