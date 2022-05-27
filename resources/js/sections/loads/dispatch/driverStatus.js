(() => {
    const tableMorning = $('#morningTable');
    const tableNight = $('#nightTable');
    const modalId = '#driverStatusModal';
    const modal = $(modalId);
    const shipper = $('#shipper');
    const getDriverStatus = () => {
        $.ajax({
            url: '/driver/search',
            data: {
                dispatch: true,
                count: true,
                shipper: shipper.val(),
            },
            success: (res) => {
                const morningTbody = tableMorning.find('tbody');
                morningTbody.empty();
                morningTbody.append(
                    `<tr><td><a class="d-block" id="morning_active" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.morning.active}</a></td>` +
                    `<td><a class="d-block" id="morning_inactive" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.morning.inactive}</a></td>` +
                    `<td><a class="d-block" id="morning_awaiting" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.morning.awaiting}</a></td>` +
                    `<td><a class="d-block" id="morning_loaded" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.morning.loaded}</a></td></tr>`
                );
                $("#morning_dispatch").html(`Morning - ${res.morning.active + res.morning.inactive}`);
                const nightTbody = tableNight.find('tbody');
                nightTbody.empty();
                nightTbody.append(
                    `<tr><td><a class="d-block" id="night_active" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.night.active}</a></td>` +
                    `<td><a class="d-block" id="night_inactive" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.night.inactive}</a></td>` +
                    `<td><a class="d-block" id="night_awaiting" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.night.awaiting}</a></td>` +
                    `<td><a class="d-block" id="night_loaded" href="${modalId}" data-toggle="modal" data-target="${modalId}">${res.night.loaded}</a></td></tr>`
                );
                $("#night_dispatch").html(`Night - ${res.night.active + res.night.inactive}`);

            },
            error: () => {
                throwErrorMsg();
            }
        });
    };
    getDriverStatus();
    let clickedType = null;
    let tbDriverStatus = null;
    const nameFormatter = (params) => {
        if (params.value)
            return params.value.name;
        else
            return '';
    };
    const carrierPhoneFormatter = (params) => {
        if (params.data && params.data.carrier.phone)
            return params.data.carrier.phone;
        else
            return '';
    };
    const trailerNumberFormatter = (params) => {
        if (params.data && params.data.truck && params.data.truck.trailer)
            return params.data.truck.trailer.number;
        else
            return '';
    };
    const truckFormatter = (params) => {
        if (params.value)
            return params.value.number;
        else
            return '';
    };
    modal.on('show.modal.bs', (e) => {
        const clicked = $(e.relatedTarget);
        const id = clicked.attr('id');
        clickedType = id.split("_");
        if (!tbDriverStatus) {
            tbDriverStatus = new tableAG({
                container: 'driverStatusTable',
                columns: [
                    {headerName: 'Truck #', field: 'truck', valueFormatter: truckFormatter},
                    {headerName: 'Chassis Nº', field: 'trailer', valueFormatter: trailerNumberFormatter},
                    {headerName: 'Driver', field: 'name'},
                    {headerName: 'Phone', field: 'phone'},
                    {headerName: 'Carrier', field: 'carrier', valueFormatter: nameFormatter},
                    {headerName: 'Carrier phone', field: 'carrier_phone', valueFormatter: carrierPhoneFormatter},
                ],
                gridOptions: {
                    components: {
                        tableRef: 'tbDriverStatus',
                    },
                },
                url: '/driver/search',
                searchQueryParams: {
                    dispatch: true,
                    type: clickedType,
                    shipper: shipper.val(),
                }
            });
        } else {
            if (tbDriverStatus.searchQueryParams.type !== clickedType) {
                tbDriverStatus.searchQueryParams.type = clickedType;
                tbDriverStatus.searchQueryParams.shipper = shipper.val();
                tbDriverStatus.updateSearchQuery();
            }
        }
    });
    shipper.change(() => {
        getDriverStatus();
    });
})();
