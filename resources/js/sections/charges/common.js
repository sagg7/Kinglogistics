(() => {
    const periodSel = $('#period'),
        carrierSel = $('[name="carriers[]"]'),
        typeSel = $('#type'),
        customP = $('#custom-period'),
        customW = $('#custom_weeks'),
        dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker');
    date.set('select', dateInp.val(), {format: 'yyyy/mm/dd'});
    typeSel.select2({
        placeholder: 'Select',
    });
    const checkPeriod = () => {
        switch (periodSel.val()) {
            case 'custom':
                customP.removeClass('d-none');
                customW.prop('disabled', false);
                break;
            default:
                customP.addClass('d-none');
                customW.prop('disabled', true);
                break;
        }
    }
    periodSel.select2({
        placeholder: 'Select',
    }).on('select2:select', function (e) {
        checkPeriod();
    });
    checkPeriod();

    $('#deleteChargeTypeModal').on('show.bs.modal', (e) => {
        let options = $('#type').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });

    carrierSel.select2({
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
    });
})();
