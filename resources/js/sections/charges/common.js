(() => {
    const periodSel = $('#period'),
        carrierSel = $('[name="carriers[]"]');
    periodSel.select2({
        placeholder: 'Select',
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
