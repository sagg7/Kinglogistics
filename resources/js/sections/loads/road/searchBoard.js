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

    function StatusBarRenderer() {}
    StatusBarRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        let colorClass;
        switch (params.value) {
            default:
                colorClass = 'bg-light';
                break;
            case 'requested':
                colorClass = 'bg-warning';
                break;
            case 'accepted':
                colorClass = 'bg-success';
                break;
            case 'rejected':
                colorClass = 'bg-danger';
                break;
        }
        this.eGui.innerHTML = `<div class="text-center ${colorClass} colors-container" style="width: 4px;">&nbsp;</div>`;
    }
    StatusBarRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    function QuickPayRenderer() {}
    QuickPayRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        this.eGui.innerHTML = `<i class="far fa-check-circle ${params.value}"></i>`;
    }
    QuickPayRenderer.prototype.getGui = () => {
        return this.eGui;
    }
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
    let selectedLoad = null;
    const dataTable = new simpleTableAG({
        id: 'dataTable',
        columns: [
            {headerName: '', field: 'status', filter: false, sortable: false, maxWidth: 14, cellRenderer: StatusBarRenderer},
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
            {headerTooltip: 'Quick Pay', headerName: 'Quick Pay', field: 'quick_pay', cellRenderer: QuickPayRenderer},
            {headerTooltip: 'Company', headerName: 'Company', field: 'shipper'},
        ],
        rowData: [],
        gridOptions: {
            components: {
                tableRef: `dataTable`,
            },
            tooltipShowDelay: 500,
            onRowClicked: (event) => {
                modal.modal('show');
                selectedLoad = event.data;
                if (selectedLoad.origin_city)
                    $('#route_string').text(`${selectedLoad.origin_city}, ${selectedLoad.origin_state} » ${selectedLoad.destination_city}, ${selectedLoad.destination_state}`);
                $('#load_details_age').text(selectedLoad.age);
                $('#load_details_mileage').text(selectedLoad.mileage);
                $('#load_details_trailer').text(selectedLoad.trailer_type);
                $('#load_details_size').text(selectedLoad.load_size);
                $('#load_details_length').text(selectedLoad.length);
                $('#load_details_weight').text(selectedLoad.weight);
                $('#load_details_width').text(selectedLoad.width);
                $('#load_details_height').text(selectedLoad.height);
                $('#load_details_payrate').text(selectedLoad.pay_rate);
                $('#load_details_date').text(selectedLoad.date);
                //$('#load_details_delivery').text(selectedLoad.);
                //$('#load_details_comments').text(selectedLoad.);
                $('#shipper_name').text(selectedLoad.shipper);
                $('#shipper_phone').text(selectedLoad.shipper_phone);
                modal.find('.modal-spinner').addClass('d-none');
                modal.find('.content-body').removeClass('d-none');
                //const selectedRows = dataTable.gridOptions.api.getSelectedRows();
                dataTable.gridOptions.api.deselectAll();
            },
        },
        autoHeight: true,
    });

    modal.on('hidden.bs.modal', () => {
        modal.find('.modal-spinner').removeClass('d-none');
        modal.find('.content-body').addClass('d-none');
        selectedLoad = null;
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
                    let colorClass;
                    if (item.shipper.factoring) {
                        colorClass = 'text-success';
                    } else if (Number(item.shipper.days_to_pay) <= 15) {
                        colorClass = 'text-warning';
                    } else {
                        colorClass = 'text-danger';
                    }
                    rowData.push({
                        status: item.road.request ? item.road.request.status : null,
                        road_load_id: item.road.id,
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
                        quick_pay: colorClass,
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

    /* ---- REQUEST MODAL FORM FUNCTIONALITY ---- */
    const requestTruck = $('#requestTruck');
    requestTruck.select2({
        ajax: {
            url: '/truck/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    type: 'hasCarrier',
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    });
    const requestLoadBtn = $('#requestLoad');
    requestLoadBtn.click(() => {
        const truck_id = requestTruck.val();
        if (!truck_id) {
            throwErrorMsg('Select the truck you want to assign to this load to continue');
        } else {
            $.ajax({
                url: '/load/road/request',
                type: 'POST',
                data: {
                    road_load_id: selectedLoad.road_load_id,
                    truck_id,
                },
                success: (res) => {
                    if (res.success) {
                        removeAjaxLoaders();
                        modal.modal('hide');
                        throwErrorMsg("Your request has been sent successfully", {"title": "Success!", "type": "success"});
                        const idx = dataTable.rowData.find(obj => obj.road_load_id === selectedLoad.road_load_id);
                        if (idx !== -1) {
                            dataTable.rowData[idx].status = 'requested';
                            dataTable.gridOptions.api.setRowData(dataTable.rowData);
                        }
                        modal.modal('hide');
                    } else {
                        throwErrorMsg();
                    }
                },
                error: () => {
                    throwErrorMsg();
                }
            }).always(() => {
                removeAjaxLoaders();
            });
        }
    });
})();
