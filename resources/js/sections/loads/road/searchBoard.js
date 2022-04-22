(() => {
    const origin_city = $('#origin_city');
    const origin_radius = $('#origin_radius');
    const destination_city = $('#destination_city');
    const destination_radius = $('#destination_radius');
    const trailer_type = $('#trailer_type');
    const load_size = $('#load_size');
    const ship_date = $('#ship_date');
    const weight = $('#weight');
    const length = $('#length');
    const modal = $('#loadDetails');

    const currencyFormatter = (value) => {
        // If not a number, return value unchanged
        if (isNaN(value))
            return value
        else // else returned the formatted value
            return numeral(value).format('$0,0.00');
    };
    const capitalizeFormatter = (value) => {
        if (value)
            return value.charAt(0).toUpperCase() + value.slice(1);
        else
            return '';
    };
    const dataTable = new simpleTableAG({
        id: 'dataTable',
        columns: [
            {headerTooltip: 'Age', headerName: 'Age', field: 'age'},
            {headerTooltip: 'Deadhead Miles', headerName: 'D/H Miles', field: 'deadhead_miles'},
            {headerTooltip: 'Trip Miles', headerName: 'Trip Miles', field: 'mileage'},
            {headerTooltip: 'Origin', headerName: 'Origin', field: 'origin_city'},
            {headerTooltip: 'State', headerName: 'ST', field: 'origin_state'},
            {headerTooltip: 'Destination', headerName: 'Destination', field: 'destination_city'},
            {headerTooltip: 'State', headerName: 'ST', field: 'destination_state'},
            {headerTooltip: 'Trailer Type', headerName: 'Trailer Type', field: 'trailer_type'},
            {headerTooltip: 'Load Size', headerName: 'Load Size', field: 'load_size'},
            {headerTooltip: 'Length', headerName: 'Length', field: 'length'},
            {headerTooltip: 'Weight', headerName: 'Weight', field: 'weight'},
            {headerTooltip: 'Payrate', headerName: 'Payrate', field: 'pay_rate'},
            {
                headerTooltip: 'Estimated Rate per Mile',
                headerName: 'Est. Rate per Mile',
                field: 'rate_mile'
            },
            {headerTooltip: 'Ship Date', headerName: 'Ship Date', field: 'date'},
            {headerTooltip: 'Quick Pay', headerName: 'Quick Pay', field: 'quick_pay'},
            {headerTooltip: 'Company', headerName: 'Company', field: 'shipper'},
        ],
        rowData: [],
        gridOptions: {
            components: {
                tableRef: `dataTable`,
            },
            tooltipShowDelay: 500,
            onRowClicked: (event) => {
                const data = event.data;
                if (data.origin_city)
                    $('#route_string').text(`${data.origin_city}, ${data.origin_state} » ${data.destination_city}, ${data.destination_state}`);
                $('#load_details_age').text(data.age);
                $('#load_details_mileage').text(data.mileage);
                $('#load_details_trailer').text(data.trailer_type);
                $('#load_details_size').text(data.load_size);
                $('#load_details_length').text(data.length);
                $('#load_details_weight').text(data.weight);
                $('#load_details_width').text(data.width);
                $('#load_details_height').text(data.height);
                $('#load_details_payrate').text(data.pay_rate);
                $('#load_details_date').text(data.date);
                //$('#load_details_delivery').text(data.);
                //$('#load_details_comments').text(data.);
                $('#shipper_name').text(data.shipper);
                $('#shipper_phone').text(data.shipper_phone);
                modal.find('.modal-spinner').addClass('d-none');
                modal.find('.content-body').removeClass('d-none');
                //const selectedRows = dataTable.gridOptions.api.getSelectedRows();
                modal.modal('show');
                dataTable.gridOptions.api.deselectAll();
            },
        },
        autoHeight: true,
    });

    const getData = (first = false) => {
        $.ajax({
            url: '/load/road/search',
            type: 'GET',
            data: {
                first,
                origin_city: origin_city.val(),
                origin_coords: selectedOrigin,
                origin_radius: origin_radius.val(),
                destination_city: destination_city.val(),
                destination_coords: selectedDestination,
                destination_radius: destination_radius.val(),
                trailer_type: trailer_type.val(),
                load_size: load_size.val(),
                ship_date_start: ship_date.data().daterangepicker.startDate.format('YYYY/MM/DD'),
                ship_date_end: ship_date.data().daterangepicker.endDate.format('YYYY/MM/DD'),
                weight: weight.val(),
                length: length.val(),
            },
            success: (res) => {
                let rowData = [];
                res.forEach(item => {
                    rowData.push({
                        age: item.age,
                        deadhead_miles: item.road.deadhead_miles, // TODO: Calculate deadhead
                        mileage: item.mileage,
                        origin_city: item.road.origin_city ? item.road.origin_city.name : null,
                        origin_state: item.road.origin_city ? item.road.origin_city.state.abbreviation : null,
                        destination_city: item.road.destination_city ? item.road.destination_city.name : null,
                        destination_state: item.road.destination_city ? item.road.destination_city.state.abbreviation : null,
                        trailer_type: item.road.trailer_type.name,
                        load_size: capitalizeFormatter(item.road.load_size),
                        length: item.road.length,
                        weight: item.weight,
                        pay_rate: item.road.pay_rate,
                        rate_mile: currencyFormatter(item.rate_mile),
                        date: item.date,
                        shipper: item.shipper.name,
                        // TODO: ADD THIS DATA
                        quick_pay: null,
                        shipper_phone: item.shipper ? item.shipper.phone : null,
                    });
                });
                dataTable.rowData = rowData;
                dataTable.gridOptions.api.setRowData(rowData);
                //dataTable.gridOptions.api.sizeColumnsToFit();
            },
            error: () => {
                throwErrorMsg();
            }
        });
    };

    const calculatePositionsAvg = (res) => {
        const locationArr = res.params.data.locations;
        if (locationArr.length === 1) {
            return {id: res.params.data.id, latitude: locationArr[0].latitude, longitude: locationArr[0].longitude};
        }
        let latitude = 0;
        let longitude = 0;
        locationArr.forEach(item => {
            latitude += Number(item.latitude);
            longitude += Number(item.longitude);
        });
        latitude = latitude / locationArr.length;
        longitude = longitude / locationArr.length;
        return {id: res.params.data.id, latitude, longitude};
    }
    let selectedOrigin = [];
    origin_city.select2({
        ajax: {
            url: '/city/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    }).on('select2:select', (res) => {
        selectedOrigin.push(calculatePositionsAvg(res));
        getData();
    }).on('select2:unselect', (res) => {
        const idx = selectedOrigin.findIndex(obj => obj.id === Number(res.params.data.id));
        selectedOrigin.splice(idx, 1);
        getData();
    });
    let selectedDestination = [];
    destination_city.select2({
        ajax: {
            url: '/city/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
    }).on('select2:select', (res) => {
        selectedDestination.push(calculatePositionsAvg(res));
        getData();
    }).on('select2:unselect', () => {
        const idx = selectedDestination.findIndex(obj => obj.id === Number(res.params.data.id));
        selectedDestination.splice(idx, 1);
        getData();
    });
    trailer_type.select2().on('select2:select', () => {
        getData();
    }).on('select2:unselect', () => {
        getData();
    });
    // Normal selects
    origin_radius.change(() => {
        getData();
    })
    destination_radius.change(() => {
        getData();
    })
    load_size.change(() => {
        getData();
    })
    weight.change(() => {
        getData();
    })
    length.change(() => {
        getData();
    })

    ship_date.daterangepicker({
        format: 'YYYY/MM/DD',
    }, (start, end, label) => {
        getData();
    });
    // First auto search
    getData(true);
})();
