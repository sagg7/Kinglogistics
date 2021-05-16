(() => {
    $('#turn_id').select2({
        ajax: {
            url: '/turn/selection',
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
    $('#truck_id').select2({
        ajax: {
            url: '/truck/selection',
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
    $('#trailer_id').select2({
        ajax: {
            url: '/trailer/selection',
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
