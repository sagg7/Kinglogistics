(() => {
    let driversChart = null;
    let carriersChart = null;
    let rowData = [];
    const dateRange = $('#dateRange');
    const carrierSel = $('[name=carrier]');
    const hourFormatter = (params) => {
        if (params.value)
            return `${params.value}h`;
        else
            return '';
    };
    const driversTable = new simpleTableAG({
        id: 'dataTable',
        columns: [
            {headerName: "Truck", field: "truck"},
            {headerName: "Driver", field: "driver"},
            {headerName: "Carrier", field: "carrier"},
            {headerName: "Waiting time", field: "waiting_time", valueFormatter: hourFormatter},
            {headerName: "Active load time", field: "loaded_time", valueFormatter: hourFormatter},
            {headerName: "Active time", field: "active_time", valueFormatter: hourFormatter},
            {headerName: "Inactive time", field: "inactive_time", valueFormatter: hourFormatter},
        ],
        rowData,
        gridOptions: {
            components: {
                tableRef: 'driversTable',
            }
        },
        autoHeight: true,
    });
    const initDriversChart = (options) => {
        const _options = _.merge({
            chart: {
                height: 350,
                type: 'bar',
            },
            title: {
                text: 'Drivers Active Time'
            },
            dataLabels: {
                enabled: true,
            },
            legend: {
                offsetY: -10
            },
            xaxis: {
                categories: [''],
            },
            yaxis: [{
                title: {
                    text: 'Active time',
                },
            }],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return Number(val);
                    }
                }
            }
        }, options);
        if (driversChart) {
            driversChart.updateOptions(_options);
        } else {
            driversChart = new ApexCharts(
                document.querySelector("#driversChart"),
                _options
            );
            driversChart.render();
        }
    }, initCarriersChart = (series, labels) => {
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
                text: 'Carriers Active Time'
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels,
            yaxis: [{
                title: {
                    text: 'Active time',
                },

            }, {
                opposite: true,
                title: {
                    text: 'Active trucks'
                }
            }]
        };
        if (carriersChart) {
            carriersChart.updateOptions(options);
        } else {
            carriersChart = new ApexCharts(
                document.querySelector("#carriersChart"),
                options
            );
            carriersChart.render();
        }
    }, getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
        $.ajax({
            url: '/report/activeTimeData',
            type: 'GET',
            data: {
                start: start.format('YYYY/MM/DD'),
                end: end.format('YYYY/MM/DD'),
                carrier: carrierSel.val(),
            },
            success: (res) => {
                rowData = [];
                let driverData = [];
                let driverNames = [];
                res.driversData.forEach(item => {
                    const carrier = res.carriersData.find(obj => obj.id === item.carrier_id);
                    rowData.push({
                        truck: item.truck ? item.truck.number : "",
                        driver: item.name,
                        carrier: carrier.name,
                        waiting_time: item.waiting_time.toFixed(2),
                        active_time: item.active_time.toFixed(2),
                        inactive_time: item.inactive_time.toFixed(2),
                        loaded_time: item.loaded_time.toFixed(2),
                    });
                    driverData.push(item.active_time.toFixed(2));
                    driverNames.push(item.name)
                });
                let carrierLabels = [];
                const carriersActiveTime = [];
                const carriersActiveTrucks = [];
                res.carriersData.forEach(item => {
                    carrierLabels.push(item.name);
                    carriersActiveTime.push(item.active_time.toFixed(2));
                    carriersActiveTrucks.push(item.trucks);
                });
                const carrierSeries = [{
                    name: 'Active time',
                    data: carriersActiveTime,
                    type: 'bar',
                }, {
                    name: 'Active trucks',
                    data: carriersActiveTrucks,
                    type: 'line',
                }];
                initDriversChart({
                    series: [{
                        name: 'Active Time',
                        data: driverData,
                    }],
                    xaxis: {
                        categories: driverNames,
                    }
                });
                initCarriersChart(carrierSeries, carrierLabels);
                driversTable.rowData = rowData;
                driversTable.gridOptions.api.setRowData(rowData);
                driversTable.gridOptions.api.sizeColumnsToFit();
            }
        });
    };
    dateRange.daterangepicker({
        format: 'YYYY/MM/DD',
        startDate: moment().startOf('week'),
        endDate: moment().endOf('week'),
    }, (start, end, label) => {
        getData(start, end);
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
})()
