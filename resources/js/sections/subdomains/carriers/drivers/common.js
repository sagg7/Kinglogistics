(() => {
    $('#turn_id').select2({
        placeholder: 'Select',
    });
    $('#zone_id').select2({
        placeholder: 'Select',
        ajax: {
            url: '/zone/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    type: 'drivers',
                };
            },
        }
    });
    $('[id="shippers[]"]').select2({
        placeholder: 'Select',
        ajax: {
            url: '/shipper/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    type: 'drivers',
                };
            },
        }
    });
})();
