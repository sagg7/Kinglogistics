(() => {
    const dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker'),
        loadTypeSel = $('#load_type_id'),
        loadTypeShipper = $('#load_type_shipper'),
        shipperSel = $('#shipper_id'),
        tripSel = $('#trip_id'),
        customerName = $('#customer_name'),
        origin = $('#origin'),
        originCoords = $('[name=origin_coords]'),
        destination = $('#destination'),
        destinationCoords = $('[name=destination_coords]'),
        mileage = $('#mileage');
    const interactive = typeof readOnly === "undefined";
    if (interactive) {
        let shipper = shipperSel.length > 0 ? shipperSel.val() : null;
        loadTypeSel.select2({
            ajax: {
                url: '/loadType/selection',
                data: (params) => {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        take: 15,
                        shipper,
                    };
                },
            },
            placeholder: 'Select',
            allowClear: true,
        });
        tripSel.select2({
            ajax: {
                url: '/trip/selection',
                data: (params) => {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        take: 15,
                        shipper,
                    };
                },
            },
            placeholder: 'Select',
            allowClear: true,
        })
            .on('select2:select', (e) => {
                $.ajax({
                    url: '/trip/getTrip',
                    type: 'GET',
                    data: {
                        id: e.params.data.id,
                    },
                    success: (res) => {
                        origin.val(res.origin);
                        originCoords.val(res.origin_coords).trigger('change');
                        destination.val(res.destination);
                        destinationCoords.val(res.destination_coords).trigger('change');
                        customerName.val(res.customer_name);
                        mileage.val(res.mileage);
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                })
            });
        if (shipperSel.length > 0) {
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
            })
                .on('select2:select', (e) => {
                    shipper = e.params.data.id;
                    loadTypeShipper.val(shipper);
                    loadTypeSel.prop('disabled', false).trigger('change');
                    tripSel.prop('disabled', false).trigger('change');
                })
                .on('select2:unselect', () => {
                    loadTypeShipper.val('');
                    loadTypeSel.val('').prop('disabled', true).trigger('change');
                    tripSel.val('').prop('disabled', true).trigger('change');
                });
            if (!loadTypeSel.val())
                loadTypeSel.prop('disabled', true).trigger('change');
        }
        date.set('select', dateInp.val(), {format: 'yyyy/mm/dd'});
        $('#deleteLoadType').on('show.bs.modal', (e) => {
            let options = $('#load_type_id').html(),
                select = $('#delete_type');
            deleteHandler(select,options);
        });
        if (shipperSel.length > 0 && !shipperSel.val())
            tripSel.prop('disabled', true).trigger('change');
    }
})();
