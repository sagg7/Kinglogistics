(() => {
    const shipperSel = $('#shipper'),
        zoneSel = $('#zone'),
        rateGroupSel = $('#rate_group');

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

    zoneSel.select2({
        ajax: {
            url: '/zone/selection',
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

    rateGroupSel.select2({
        placeholder: 'Select',
    });

    $('#deleteRateGroup').on('show.bs.modal', (e) => {
        let options = $('#rate_group').html(),
            select = $('#delete_type');
        deleteHandler(select,options);
    });
})();
