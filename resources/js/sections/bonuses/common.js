(() => {
    const carrierSel = $('[name="carriers[]"]'),
        typeSel = $('#type');
    typeSel.select2({
        placeholder: 'Select',
    });

    $('#deleteBonusTypeModal').on('show.bs.modal', (e) => {
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
