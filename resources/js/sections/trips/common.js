(() => {
    const shipper = $('#shipper_id'),
        zone = $('#zone_id'),
        rate = $('#rate_id'),
        status = $('#status'),
        origin = $('#origin_id'),
        destination = $('#destination_id');
    zone.select2({
        placeholder: 'Select',
        ajax: {
            url: '/zone/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    }).on('select2:select', (e) => {
        checkValidRate();
    }).on('select2:unselect', (e) => {
        checkValidRate();
    });
    shipper.select2({
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
    }).on('select2:select', (e) => {
        checkValidRate();
    }).on('select2:unselect', (e) => {
        checkValidRate();
    });
    origin.select2({
        ajax: {
            url: '/trip/origin/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            }
        },
        placeholder: 'Select',
        allowClear: true,
    });
    destination.select2({
        ajax: {
            url: '/trip/destination/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            }
        },
        placeholder: 'Select',
        allowClear: true,
    });
    const checkValidRate = () => {
        return rate.prop('disabled', shipper.val() === "" || zone.val() === "");
    }
    rate.select2({
        ajax: {
            url: '/rate/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    shipper: shipper.val(),
                    zone: zone.val(),
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    });
    status.select2({
        placeholder: 'Select',
    });
    checkValidRate();
})();
