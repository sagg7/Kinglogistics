(() => {
    let driversChart = null;
    let carriersChart = null;
    let rowData = [];
    const dateRange = $('#dateRange');
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
    const initDriversChart = (series) => {
        const options = {
            series,
            chart: {
                height: 350,
                type: 'bar',
            },
            title: {
                text: 'Drivers Active Time'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'flat',
                    columnWidth: '55%',
                },
            },
            dataLabels: {
                enabled: true,
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
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
            fill: {
                opacity: 1

            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return Number(val);
                    }
                }
            }
        }
        if (driversChart) {
            driversChart.updateOptions(options);
        } else {
            driversChart = new ApexCharts(
                document.querySelector("#driversChart"),
                options
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
            },
            success: (res) => {
                rowData = [];
                let driverSeries = [];
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
                    driverSeries.push({
                        name: item.name,
                        data: [item.active_time.toFixed(2)],
                    });
                });
                let carrierLabels = [];
                const carriersActiveTime = [];
                const carriersActiveTrucks = [];
                res.carriersData.forEach(item => {
                    carrierLabels.push(item.name);
                    carriersActiveTime.push(item.active_time.toFixed(2));
                    carriersActiveTrucks.push(item.trucks);
                });
                const carrierSeries= [{
                    name: 'Active time',
                    data: carriersActiveTime,
                    type: 'bar',
                }, {
                    name: 'Active trucks',
                    data: carriersActiveTrucks,
                    type: 'line',
                }];
                initDriversChart(driverSeries);
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
    getData();
})()
