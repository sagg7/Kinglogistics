(() => {
    $('#trailer_id').select2({
        placeholder: 'Select',
        ajax: {
            url: '/trailer/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    });
    $('#driver_id').select2({
        placeholder: 'Select',
        ajax: {
            url: '/driver/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    });
})();
