(() => {
    let carrier = null;
    const driverSel = $('#driver_id'),
        trailerSel = $('#trailer_id'),
        dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker');
    date.set('select', dateInp.val(), {format: 'yyyy/mm/dd'});
    $('#period').select2({
        placeholder: 'Select',
    });
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
            //trailerSel.val('').trigger('change');
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
                    rental: 1,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    })
        .on('select2:select', (res) => {
            /*const driver = res.params.data,
                trailer = driver.truck.trailer;
            trailerSel.html(`<option value="${trailer.id}">${trailer.number}</option>`)
                .val(`${trailer.id}`)
                .trigger('change');*/
        })
        .on('select2:unselect', () => {
            //trailerSel.val('').trigger('change');
        });
    trailerSel.select2({
        ajax: {
            url: '/trailer/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    carrier,
                    rental: 1,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    });
    driverSel.prop('disabled', true);
})();
