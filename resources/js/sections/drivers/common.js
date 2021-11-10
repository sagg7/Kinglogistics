(() => {
    $('#turn_id').select2({
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
    });
})();
