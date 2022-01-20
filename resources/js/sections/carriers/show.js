let _gridTrailers;
let _gridDrivers;
(() => {
    const currencyFormatter = (params) => {
        return numeral(params.value).format('$0,0.00');
    }
    const capitalizeFormatter = (params) => {
        if (params.value)
            return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
        else
            return '';
    };
    let trailersData = [];
    let driversData = [];
    _gridTrailers = new simpleTableAG({
        id: 'trailersTable',
        columns: [
            {
                headerName: "Name",
                field: "name",
            },
            {
                headerName: "Status",
                field: "status",
                valueFormatter: capitalizeFormatter,
            },
            {
                headerName: "Cost",
                field: "cost",
                valueFormatter: currencyFormatter,
            },
        ],
        gridOptions: {
            components: {
                tableRef: 'window[gridName]',
            },
        },
        rowData: trailersData,
    });
    _gridDrivers = new simpleTableAG({
        id: 'driversTable',
        columns: [
            {
                headerName: "Name",
                field: "name",
            },
            {
                headerName: "Loads",
                field: "loads",
            },
        ],
        gridOptions: {
            components: {
                tableRef: 'window[gridName]',
            },
        },
        rowData: driversData,
    });

    const yearIncome = $('#yearIncome');
    const monthIncome = $('#monthIncome');
    const lastWeekIncome = $('#lastWeekIncome');
    const weekIncome = $('#weekIncome');
    const ranking = $('#ranking');
    const lastWeekLoads = $('#lastWeekLoads');
    const weekLoads = $('#weekLoads');
    const incidents = $('#incidents');
    const carrierStatus = $('#status');
    const formatCurrency = () => {
        $.each($('.currency'), (i, el) => {
            const element = $(el);
            element.text(numeral(element.text()).format('$0,0.00'));
        });
    }
    const fillTableDrivers = () => {
        _gridDrivers.rowData = driversData;
        _gridDrivers.gridOptions.api.setRowData(driversData);
        _gridDrivers.grid.gridOptions.api.setPinnedBottomRowData(_gridDrivers.pinnedBottomFunction(_gridDrivers));
        _gridDrivers.gridOptions.api.sizeColumnsToFit();
    }
    const fillTableTrailers = () => {
        _gridTrailers.rowData = trailersData;
        _gridTrailers.gridOptions.api.setRowData(trailersData);
        _gridTrailers.grid.gridOptions.api.setPinnedBottomRowData(_gridTrailers.pinnedBottomFunction(_gridTrailers));
        _gridTrailers.gridOptions.api.sizeColumnsToFit();
    }
    let url = `/carrier/summaryData/`;
    if (typeof carrier_id !== "undefined") {
        url += carrier_id;
    }
    $.ajax({
        url,
        success: (res) => {
            // Set Currency Values
            yearIncome.text(res.incomeYear);
            monthIncome.text(res.incomePastMonth);
            lastWeekIncome.text(res.incomePastWeek);
            weekIncome.text(res.incomeWeek);
            ranking.text(res.ranking);
            formatCurrency();
            // Set Simple Data
            carrierStatus.text(res.status);
            lastWeekLoads.text(res.totalLoadsPastWeek);
            weekLoads.text(res.totalLoadsWeek);
            incidents.text(res.incidents);
            // Set Drivers Table
            res.drivers.forEach(item => {
                driversData.push({
                    name: item.name,
                    loads: item.loads_count,
                });
            });
            fillTableDrivers();
            // Set Rentals Table
            res.rentals.forEach(item => {
                trailersData.push({
                    name: item.trailer.number,
                    status: item.trailer.status,
                    cost: item.cost,
                });
            });
            fillTableTrailers()
        }
    });
})();
