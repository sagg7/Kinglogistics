(() => {
    let carrier = null;
    const driverSel = $('#driver_id'),
        truckSel = $('#truck_id'),
        trailerSel = $('#trailer_id'),
        dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker');
    date.set('select', dateInp.val(), {format: 'yyyy/mm/dd'});
    $('#carrier_id').select2({
        ajax: {
            url: '/carrier/selection',
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
    })
        .on('select2:select', (res) => {
            carrier = res.params.data.id;
            driverSel.prop('disabled', false).trigger('change');
        })
        .on('select2:unselect', () => {
            carrier = null;
            driverSel.val('').trigger('change');
            truckSel.val('').trigger('change');
            trailerSel.val('').trigger('change');
            driverSel.prop('disabled', true).trigger('change');
        });
    driverSel.select2({
        ajax: {
            url: '/driver/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    carrier,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    })
        .on('select2:select', (res) => {
            const driver = res.params.data,
                truck = driver.truck,
                trailer = truck.trailer;
            truckSel.html(`<option value="${truck.id}">${truck.number}</option>`)
                .val(`${truck.id}`)
                .trigger('change');
            trailerSel.html(`<option value="${trailer.id}">${trailer.number}</option>`)
                .val(`${trailer.id}`)
                .trigger('change');
        })
        .on('select2:unselect', () => {
            truckSel.val('').trigger('change');
            trailerSel.val('').trigger('change');
        });
    truckSel.select2({
        ajax: {
            url: '/truck/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    carrier,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    });
    trailerSel.select2({
        ajax: {
            url: '/trailer/selection',
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
    if (!driverSel.val())
        driverSel.prop('disabled', true).trigger('change');
    $('#incident_type_id').select2({
        placeholder: 'Select',
    });
    $('#sanction').select2({
        placeholder: 'Select',
    });
    $('#deleteIncidentType').on('show.bs.modal', (e) => {
        let options = $('#incident_type_id').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
