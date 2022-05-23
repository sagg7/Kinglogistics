(() => {
    const dateInp = $('#date'),
        date = initPickadate(dateInp).pickadate('picker'),
        loadTypeSel = $('#load_type_id'),
        loadTypeShipper = $('#load_type_shipper'),
        shipperSel = $('#shipper_id'),
        tripSel = $('#trip_id'),
        originSel = $('#origin_id'),
        destinationSel = $('#destination_id'),
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
                        origin.val(res.trip_origin ? res.trip_origin.name : '');
                        originCoords.val(res.trip_origin ? res.trip_origin.coords : '').trigger('change');
                        destination.val(res.trip_destination ? res.trip_destination.name : '');
                        destinationCoords.val(res.trip_destination ? res.trip_destination.coords : '').trigger('change');
                        customerName.val(res.customer_name);
                        mileage.val(res.mileage);
                        if (res.trip_origin) {
                            originSel.append(`<option value="${res.trip_origin.id}">${res.trip_origin.name}</option>`);
                            originSel.val(res.trip_origin.id).prop('disabled', true);
                        }
                        if (res.trip_destination) {
                            destinationSel.append(`<option value="${res.trip_destination.id}">${res.trip_destination.name}</option>`);
                            destinationSel.val(res.trip_destination.id).prop('disabled', true);
                        }
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                })
            })
            .on('select2:unselect', () => {
                originSel.val('').prop('disabled', false).trigger('change');
                destinationSel.val('').prop('disabled', false).trigger('change');
            });
        originSel.select2({
            ajax: {
                url: '/trip/origin/selection',
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
        }).on('select2:select', (res) => {
            const data = res.params.data;
            origin.val(data.text);
            originCoords.val(data.coords).trigger('change');
        });
        destinationSel.select2({
            ajax: {
                url: '/trip/destination/selection',
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
        }).on('select2:select', (res) => {
            const data = res.params.data;
            destination.val(data.text);
            destinationCoords.val(data.coords).trigger('change');
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
            if (!loadTypeSel.val() && !shipperSel.val())
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
        if (tripSel.val()) {
            originSel.prop('disabled', true);
            destinationSel.prop('disabled', true);
        }
        const createLoadModal = $('#createLoadModal');
        if (createLoadModal.length > 0) {
            createLoadModal.on('hidden.modal.bs', () => {
                createLoadModal.find('input, select, textarea').val('').trigger('change');
                createLoadModal.find('#shipper_id').trigger('select2:unselect');
            });
            $('#loadForm').submit(e => {
                e.preventDefault();
                const form = $(e.currentTarget);
                const formData = new FormData(form[0]);
                const url = form.attr('action');
                $.ajax({
                    url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: (res) => {
                        if (res.success) {
                            // If it has notes, the load is stored as finished, thus updating the finished table
                            if (formData.get('notes') === "finished") {
                                tbLoadActive.updateSearchQuery();
                            } else { // Else update the active table
                                tbLoadFinished.updateSearchQuery();
                            }
                            createLoadModal.modal('hide');
                        } else {
                            throwErrorMsg();
                        }
                    },
                    error: (res) => {
                        let errors = `<ul class="text-left">`;
                        Object.values(res.responseJSON.errors).forEach(msgs => {
                            msgs.forEach(msg => {
                                errors += `<li>${msg}</li>`;
                            });
                        });
                        errors += `</ul>`;
                        throwErrorMsg(errors, {timer: false});
                    }
                }).always(() => {
                    removeAjaxLoaders();
                });
            });
        }
    }
})();
