(() => {
    const trailer = $('#trailer_id'),
        driver = $('#driver_id'),
        seller = $('#seller_id'),
        carrier = $('#carrier_id');
    trailer.select2({
        placeholder: 'Select',
        ajax: {
            url: '/trailer/selection',
            data: (params) => {
                console.log(carrier);
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    carrier: carrier.val(),
                };
            },
        },
        carrier: carrier.val(),
    });
    driver.select2({
        placeholder: 'Select',
        ajax: {
            url: '/driver/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    carrier: carrier.val(),
                    noTruck: 1,
                };
            },
        }
    });
    seller.select2({
        placeholder: 'Select',
        ajax: {
            url: '/user/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    type: 'seller',
                };
            },
        }
    });
    // carrier.select2({
    //     ajax: {
    //         url: '/carrier/selection',
    //         data: (params) => {
    //             return {
    //                 search: params.term,
    //                 page: params.page || 1,
    //                 take: 15,
    //             };
    //         },
    //     },
    //     placeholder: 'Select',
    //     allowClear: true,
    // });
})();
