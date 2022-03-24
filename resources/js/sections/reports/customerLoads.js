(() => {
    let chart = null;
    let activeRowData = [];
    let inactiveRowData = [];
    const dateRange = $('#dateRange');
    const shipperSel = $('#shipper');
    const carrierSel = $('#carrier');
    const modalLoads = $('#loads-data');
    const modalCarriers = $('#carrier-data');
    const modalDriver = $('#driver-data');
    let activeCarrierDriversTable;
    let inactiveCarrierDriversTable;
    let inactiveLoadsDriversTable;
    const modalLoadsTable = new simpleTableAG({
        id: `totalLoads`,
        columns: [
            {headerName: "Finished at", field: "date"},
            {headerName: "Control #", field: "control"},
            {headerName: "C Reference ", field: "reference"},
            {headerName: "Bol", field: "bol"},
            {headerName: "Job", field: "job"},
        ],
        rowData: [],
        gridOptions: {
            components: {
                tableRef: `modalLoadsTable`,
            }
        },
        autoHeight: true,
    });
    const modalDriverTable = new simpleTableAG({
        id: `driverTable`,
        columns: [
            {headerName: "Name", field: "driver"},
            {headerName: "Phone", field: "driver_phone"},
            {headerName: "Truck", field: "truck"},
            {headerName: "Carrier", field: "carrier", cellRenderer: CarrierModalRenderer},
            {headerName: "Carrier Phone", field: "carrier_phone"},
        ],
        rowData: [],
        gridOptions: {
            components: {
                tableRef: `modalDriverTable`,
            }
        },
        autoHeight: true,
    });
    function LoadsModalRenderer() {}
    LoadsModalRenderer.prototype.init = (params) => {
        const id = OptionsRenderer.prototype.guidGenerator();
        this.eGui = document.createElement('div');
        if (params.value) {
            this.eGui.innerHTML = `<a href="#" id="${id}">${params.value}</a>`;
            $(function () {
                $(`#${id}`).click((e) => {
                    e.preventDefault();
                    $('.modal.show').modal('hide');
                    modalLoads.modal('show');
                    const modalContent = modalLoads.find('.content-body');
                    const modalSpinner = modalLoads.find('.modal-spinner');
                    modalContent.addClass('d-none');
                    modalSpinner.removeClass('d-none');
                    if (!inactiveLoadsDriversTable) {
                        inactiveLoadsDriversTable = initTable('inactive', 'loads');
                    }
                    const data = activeDriversTable.rowData.filter(item => {
                        return item.carrier_id === params.data.carrier_id &&
                            item.driver_id === params.data.driver_id &&
                            item.shipper_id === params.data.shipper_id;
                    });
                    inactiveLoadsDriversTable.rowData = data;
                    inactiveLoadsDriversTable.gridOptions.api.setRowData(data);
                    inactiveLoadsDriversTable.gridOptions.api.sizeColumnsToFit();
                    modalLoadsTable.rowData = params.data.load_data;
                    modalLoadsTable.gridOptions.api.setRowData(params.data.load_data);
                    modalLoadsTable.gridOptions.api.sizeColumnsToFit();
                    modalContent.removeClass('d-none');
                    modalSpinner.addClass('d-none');
                });
            });
        }
    }
    LoadsModalRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    function CarrierModalRenderer() {}
    CarrierModalRenderer.prototype.init = (params) => {
        const id = OptionsRenderer.prototype.guidGenerator();
        this.eGui = document.createElement('div');
        if (params.value) {
            this.eGui.innerHTML = `<a href="#" id="${id}">${params.value}</a>`;
            $(function () {
                $(`#${id}`).click((e) => {
                    e.preventDefault();
                    $('.modal.show').modal('hide');
                    modalCarriers.modal('show');
                    const modalContent = modalCarriers.find('.content-body');
                    const modalSpinner = modalCarriers.find('.modal-spinner');
                    const modalInfo = modalCarriers.find('#carrier-info tbody tr');
                    modalContent.addClass('d-none');
                    modalSpinner.removeClass('d-none');
                    modalInfo.html(`<td>${params.data.carrier}</td><td>${params.data.carrier_phone ? params.data.carrier_phone : ''}</td>`);
                    if (!activeCarrierDriversTable) {
                        activeCarrierDriversTable = initTable('active', 'carrier');
                        inactiveCarrierDriversTable = initTable('inactive', 'carrier');
                    }
                    const activeData = activeDriversTable.rowData.filter(item => {
                        return item.carrier_id === params.data.carrier_id;
                    });
                    const inactiveData = inactiveDriversTable.rowData.filter(item => {
                        return item.carrier_id === params.data.carrier_id;
                    });
                    activeCarrierDriversTable.rowData = activeData;
                    activeCarrierDriversTable.gridOptions.api.setRowData(activeData);
                    activeCarrierDriversTable.gridOptions.api.sizeColumnsToFit();
                    inactiveCarrierDriversTable.rowData = inactiveData;
                    inactiveCarrierDriversTable.gridOptions.api.setRowData(inactiveData);
                    inactiveCarrierDriversTable.gridOptions.api.sizeColumnsToFit();
                    modalContent.removeClass('d-none');
                    modalSpinner.addClass('d-none');
                });
            });
        }
    }
    CarrierModalRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    function DriverModalRenderer() {}
    DriverModalRenderer.prototype.init = (params) => {
        const id = OptionsRenderer.prototype.guidGenerator();
        this.eGui = document.createElement('div');
        if (params.value) {
            this.eGui.innerHTML = `<a href="#" id="${id}">${params.value}</a>`;
            $(function () {
                $(`#${id}`).click((e) => {
                    e.preventDefault();
                    $('.modal.show').modal('hide');
                    modalDriver.modal('show');
                    const modalContent = modalDriver.find('.content-body');
                    const modalSpinner = modalDriver.find('.modal-spinner');
                    modalDriverTable.rowData = [params.data];
                    modalDriverTable.gridOptions.api.setRowData([params.data]);
                    modalDriverTable.gridOptions.api.sizeColumnsToFit();
                    modalContent.removeClass('d-none');
                    modalSpinner.addClass('d-none');
                });
            });
        }
    }
    DriverModalRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    const initChart = (series, labels) => {
        const options = {
            series,
            chart: {
                height: 350,
                type: 'line',
            },
            stroke: {
                width: [0, 4]
            },
            title: {
                text: 'Totals by Customer'
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels,
            yaxis: [{
                title: {
                    text: 'Total Loads',
                },

            }, {
                opposite: true,
                title: {
                    text: 'Active trucks'
                }
            }]
        };
        if (chart) {
            chart.updateOptions(options);
        } else {
            chart = new ApexCharts(
                document.querySelector("#chart"),
                options
            );
            chart.render();
        }
    };
    const capitalizeNameFormatter = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };
    const initTable = (type, user_type) => {
        let columns = [
            {headerName: "Truck", field: "truck"},
        ];
        if (type === 'active') {
            columns.push({headerName: "Total Loads", field: "loads", cellRenderer: LoadsModalRenderer});
        }
        columns.push({headerName: "Driver", field: "driver", cellRenderer: DriverModalRenderer});
        if (user_type !== 'carrier') {
            columns.push({headerName: "Carrier", field: "carrier", cellRenderer: CarrierModalRenderer});
        }
        columns.push({headerName: "Customer", field: "shipper"});
        const capitalizedUser = capitalizeNameFormatter(user_type);
        return new simpleTableAG({
            id: `${type}${capitalizedUser}Drivers`,
            columns,
            rowData: [],
            gridOptions: {
                components: {
                    tableRef: `${type}${capitalizedUser}DriversTable`,
                }
            },
            autoHeight: true,
        });
    }
    const activeDriversTable = initTable('active', 'general');
    const inactiveDriversTable = initTable('inactive', 'general');
    const getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
        $.ajax({
            url: '/report/customerLoadsData',
            type: 'GET',
            data: {
                start: start.format('YYYY/MM/DD'),
                end: end.format('YYYY/MM/DD'),
                shipper: shipperSel.val(),
                carrier: carrierSel.val(),
            },
            success: (res) => {
                const createRowObj = (driver, shipper = null, load_count = null) => {
                    return {
                        truck: driver.truck ? driver.truck.number : null,
                        loads: load_count ? load_count : driver.load_count,
                        driver: driver.name,
                        driver_id: driver.id,
                        carrier: driver.carrier.name,
                        carrier_id: driver.carrier.id,
                        carrier_phone: driver.carrier.phone,
                        shipper: shipper ? shipper.name : null,
                        shipper_id: shipper ? shipper.id : null,
                    };
                }
                activeRowData = [];
                inactiveRowData = [];
                const chartSeries = [{
                    name: 'Total loads',
                    data: [],
                    type: 'bar',
                }, {
                    name: 'Active trucks',
                    data: [],
                    type: 'line',
                }];
                const chartLabels = [];
                const shipperData = [];
                const addShipper = (item) => {
                    const shipper = shipperData.find(obj => obj.id === item.shipper_id);
                    if (shipper) {
                        shipper.loads += item.loads;
                        if (item.truck_id) {
                            const truckIdx = shipper.trucks.findIndex((val) => val === item.truck_id);
                            if (truckIdx === -1) {
                                shipper.trucks.push(item.truck_id);
                            }
                        }
                    } else {
                        shipperData.push({
                            id: item.shipper_id,
                            name: item.shipper,
                            loads: item.loads,
                            trucks: [],
                        });
                        if (item.truck_id) {
                            shipperData[shipperData.length - 1].trucks.push(item.truck_id);
                        }
                    }
                }
                res.active_data.forEach(item => {
                    addShipper(item);
                });
                res.inactive_data.forEach(item => {
                    item.shippers.forEach(shipper => {
                        addShipper({
                            shipper_id: shipper.id,
                            shipper: shipper.name,
                            loads: 0,
                            //truck_id: item.truck_id,
                        });
                        inactiveRowData.push(_.merge(item, {
                            shipper: shipper.name,
                            shipper_id: shipper.id,
                        }));
                    });
                });
                shipperData.forEach(item => {
                    chartSeries[0].data.push(item.loads);
                    chartSeries[1].data.push(item.trucks.length);
                    chartLabels.push(item.name);
                });
                // Init the chart for total loads/active trucks
                initChart(chartSeries, chartLabels);
                activeRowData = res.active_data;
                // Set the active drivers table data
                activeDriversTable.rowData = activeRowData;
                activeDriversTable.gridOptions.api.setRowData(activeRowData);
                activeDriversTable.gridOptions.api.sizeColumnsToFit();
                // Set the inactive drivers table data
                inactiveDriversTable.rowData = inactiveRowData;
                inactiveDriversTable.gridOptions.api.setRowData(inactiveRowData);
                inactiveDriversTable.gridOptions.api.sizeColumnsToFit();
            },
            error: () => {
                throwErrorMsg();
            }
        });
    };
    dateRange.daterangepicker({
        format: 'YYYY/MM/DD',
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
    }, (start, end, label) => {
        getData(start, end);
    });
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
    }).on('select2:select', () => {
        getData();
    }).on('select2:unselect', () => {
        getData();
    });
    carrierSel.select2({
        ajax: {
            url: '/carrier/selection',
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
    }).on('select2:select', () => {
        getData();
    }).on('select2:unselect', () => {
        getData();
    });
    getData();
})();
