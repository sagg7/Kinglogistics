(() => {
    const modal = $('#requestDetails');
    let selectedLoad = null;
    function QuickPayRenderer() {}
    QuickPayRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        this.eGui.innerHTML = `<i class="far fa-check-circle ${params.value}"></i>`;
    }
    QuickPayRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    function AcceptBtnRenderer() {}
    AcceptBtnRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        this.eGui.innerHTML = `<button class="btn btn-success submit-ajax" id="loadRequest-${params.value}">Accept</button>`;
        $(function () {
            $(`#loadRequest-${params.value}`).click((e) => {
                const btn = $(e.currentTarget);
                btn.html(`<span class="spinner-border spinner-border-sm ajax-loader" role="status" aria-hidden="true"></span>`);
                setTimeout(() => {
                    btn.prop('disabled', true);
                }, 3)
                $.ajax({
                    url: '/load/road/acceptRequest',
                    type: 'POST',
                    data: {
                        road_load_id: selectedLoad.road_load_id,
                        request_id: params.value,
                    },
                    success: (res) => {
                        if (res.success) {
                            tbAG.updateSearchQuery();
                            modal.modal('hide');
                            selectedLoad = null;
                            throwErrorMsg("The request has been accepted", {"title": "Success!", "type": "success"});
                        } else {
                            throwErrorMsg();
                        }
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                }).always(() => {
                    btn.html('Accept');
                    btn.prop('disabled', false);
                });
            });
        });
    }
    AcceptBtnRenderer.prototype.getGui = () => {
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
    const requestsTable = new simpleTableAG({
        id: 'requestsTable',
        columns: [
            {headerName: 'Carrier', field: 'carrier'},
            {headerName: 'Truck', field: 'truck'},
            {headerName: 'Accept Request', field: 'id', cellRenderer: AcceptBtnRenderer},
        ],
        rowData: [],
        gridOptions: {
            components: {
                tableRef: `requestsTable`,
            },
        },
        autoHeight: true,
    });
    tbAG = new tableAG({
        columns: [
            {headerTooltip: 'Age', headerName: 'Age', field: 'age'},
            {headerTooltip: 'Deadhead Miles', headerName: 'D/H Miles', field: 'deadhead_miles', filter: false, sortable: false,},
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
                field: 'rate_mile', filter: false, sortable: false,
            },
            {headerTooltip: 'Ship Date', headerName: 'Ship Date', field: 'date'},
            {headerTooltip: 'Quick Pay', headerName: 'Quick Pay', field: 'quick_pay', cellRenderer: QuickPayRenderer, filter: false, sortable: false,},
            {headerTooltip: 'Company', headerName: 'Company', field: 'shipper'},
        ],
        gridOptions: {
            undoRedoCellEditing: true,
            onRowClicked: (event) => {
                modal.modal('show');
                tbAG.gridOptions.api.deselectAll();
                if (!selectedLoad || selectedLoad.road_load_id !== event.data.road_load_id) {
                    selectedLoad = event.data;
                    $.ajax({
                        url: '/load/road/getRequests',
                        type: 'GET',
                        data: {
                            road_load_id: selectedLoad.road_load_id,
                        },
                        success: (res) => {
                            const rowData = [];
                            res.forEach(item => {
                                rowData.push({
                                    id: item.id,
                                    carrier: item.carrier.name,
                                    truck: item.truck.number,
                                });
                            });
                            requestsTable.rowData = rowData;
                            requestsTable.gridOptions.api.setRowData(rowData);
                            modal.find('.modal-spinner').addClass('d-none');
                            modal.find('.content-body').removeClass('d-none');
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    });
                } else {
                    modal.find('.modal-spinner').addClass('d-none');
                    modal.find('.content-body').removeClass('d-none');
                }
            },
        },
        container: 'myGrid',
        url: '/load/road/search',
        tableRef: 'tbAG',
        searchQueryParams: {
            dispatch: true,
        },
        formatDSResult: (res) => {
            const rowData = [];
            res.rows.forEach(item => {
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
            return {rows: rowData, lastRow: res.lastRow};
        },
    });
    modal.on('hidden.bs.modal', () => {
        modal.find('.modal-spinner').removeClass('d-none');
        modal.find('.content-body').addClass('d-none');
    });
})();
