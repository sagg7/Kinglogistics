(() => {
    const shipperSel = $('[name=shipper]');
    const tripSel = $('[name=trips]');
    const driverSel = $('[name=driver]');
    let shipper = null;
    let trip = null;
    let driver = null;
    const loadSummary = [];
    const summaryArea = $('#loads-summary');
    const summaryTable = summaryArea.find('table');
    //const activeDrivers = [];
    const getLoadsData = () => {
        summaryTable.find('h2').text(0);
        $.ajax({
            url: '/dashboard/getData',
            type: 'GET',
            data: {
                shipper,
                trip,
                driver,
            },
            success: (res) => {
                if (res.loads) {
                    Object.entries(res.loads).forEach(item => {
                        const [key, value] = item;
                        $(`#${key}_summary`).text(value.count);
                        //const countUp = new CountUp(`${key}_summary`, value.count);
                        //!countUp.error ? countUp.start() : console.error(countUp.error);
                        loadSummary.push({
                            status: key,
                            count: value.count,
                            data: value.data,
                        });
                        /*if (driverSel.is(':empty')) {
                            value.data.forEach((item) => {
                                const driver = item.driver ? activeDrivers.find(obj => Number(obj.id) === Number(item.driver.id)) : null;
                                if (!driver && driver !== null)
                                    activeDrivers.push({
                                        id: item.driver.id,
                                        name: item.driver.name,
                                    });
                            });
                        }*/
                    });
                    /*if (driverSel.is(':empty')) {
                        activeDrivers.sort((a,b) => (a.id > b.id) ? 1 : ((b.id > a.id) ? -1 : 0))
                        let html = '<option></option>';
                        activeDrivers.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        driverSel.html(html);
                    }*/
                }
            },
            error: () => {
                throwErrorMsg();
            }
        });
    };
    /*
     * TABLES
     */
    const getRole = (params) => {
        if (params.data)
            return params.data.roles[0].name;
    };
    const percentageFormatter = (params) => {
        if (params.value)
            return `${params.value.toFixed(2)}%`;
        else
            return '';
    }
    function PhoneCallRenderer() {}
    PhoneCallRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        if (params.value) {
            this.eGui.innerHTML = `<a href="tel:${params.value}">${params.value}</a>`;
        }
    }
    PhoneCallRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    function MailToRenderer() {}
    MailToRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        if (params.value) {
            this.eGui.innerHTML = `<a href="mailto:${params.value}">${params.value}</a>`;
        }
    }
    MailToRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    const tripsStatusFormatter = (params) => {
        if (params.value)
            return params.value.charAt(0).toUpperCase()  + params.value.slice(1)
                + ` ${params.data.status_current} of ${params.data.status_total}`;
        else
            return '';
    };
    const minutesAVGFormatter = (params) => {
        if (params.value)
            return `${params.value.toFixed(2)} min`;
        else
            return '';
    }
    const fillOnCallTable = () => {
        $.ajax({
            type: 'GET',
            url: '/user/searchActive',
            data: {
                all: true,
            },
            success: (res) => {
                tbOnCall.rowData = res;
                tbOnCall.gridOptions.api.setRowData(res);
                tbOnCall.gridOptions.api.sizeColumnsToFit();
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
    const fillJobsTable = () => {
        $.ajax({
            type: 'GET',
            url: '/trip/dashboardData',
            success: (res) => {
                tbJobs.rowData = res;
                tbJobs.gridOptions.api.setRowData(res);
                tbJobs.gridOptions.api.sizeColumnsToFit();
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
    if (typeof tbOnCall !== "undefined") {
        tbOnCall = new simpleTableAG({
            id: 'onCallTable',
            columns: [
                {headerName: 'Name', field: 'name'},
                {headerName: 'Role', field: 'role', valueFormatter: getRole},
                {headerName: 'Email', field: 'email', cellRenderer: MailToRenderer},
                {headerName: 'Phone', field: 'phone', cellRenderer: PhoneCallRenderer},
            ],
            rowData: [],
            gridOptions: {
                components: {
                    tableRef: 'tbOnCall',
                },
            },
        });
        fillOnCallTable();
    }
    if (typeof tbJobs !== "undefined") {
        tbJobs = new simpleTableAG({
            id: 'jobsTable',
            columns: [
                {headerName: 'Name', field: 'name'},
                {headerName: 'Status', field: 'status', valueFormatter: tripsStatusFormatter},
                {headerName: 'Percentage', field: 'percentage', valueFormatter: percentageFormatter},
                {headerName: 'Miles', field: 'mileage'},
                {headerName: 'AVG', field: 'avg', valueFormatter: minutesAVGFormatter},
                {headerName: 'Load time', field: 'load_time', valueFormatter: minutesAVGFormatter},
            ],
            rowData: [],
            gridOptions: {
                components: {
                    tableRef: 'tbJobs',
                },
            },
        });
        fillJobsTable();
    }
    /*
     * CHARTS
     */

    const barChart = (chartId, series, config) => {
        // Column Chart
        // ----------------------------------
        const options = {
            chart: {
                height: 350,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'flat',
                    columnWidth: '55%',
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series,
            legend: {
                offsetY: -10
            },
            xaxis: {
                categories: [''],
            },
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
        _.merge(options, config);
        const barChart = new ApexCharts(
            document.querySelector(`#${chartId}`),
            options
        );
        barChart.render();

        return barChart;
    };
    const stackedBarChart = (chartId, series, config) => {
        const options = {
            series,
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                categories: [
                    'Morning',
                    'Night',
                ],
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            }
        };
        _.merge(options, config);
        const barChart = new ApexCharts(
            document.querySelector(`#${chartId}`),
            options
        );
        barChart.render();

        return barChart;
    }
    let driversChart = null;
    const showDriversChart = () => {
        if (guard !== 'web') {
            return false;
        }
        $.ajax({
            url: '/driver/search',
            type: 'GET',
            data: {
                graph: true,
                shipper,
                trip,
                driver,
            },
            success: (res) => {
                const series = [{
                    name: 'Active',
                    data: [res.morning.active, res.night.active]
                }, {
                    name: 'Inactive',
                    data: [res.morning.inactive, res.night.inactive]
                }, {
                    name: 'Ready',
                    data: [res.morning.ready, res.night.ready]
                }, {
                    name: 'Pending',
                    data: [res.morning.pending, res.night.pending]
                }, {
                    name: 'Error',
                    data: [res.morning.error, res.night.error]
                }]
                if (!driversChart) {
                    const config = {
                        colors: [chartColorsObj.primary, chartColorsObj.info, chartColorsObj.success, chartColorsObj.warning, chartColorsObj.danger],
                    }
                    driversChart = stackedBarChart('driversChart', series, config);
                } else {
                    driversChart.updateSeries(series);
                }
            }
        });
    }
    let trailersChart = null;
    const showTrailersChart = () => {
        if (guard !== 'web') {
            return false;
        }
        $.ajax({
            url: '/trailer/search',
            type: 'GET',
            data: {
                graph: true,
                shipper,
                trip,
                driver,
            },
            success: (res) => {
                const series = [{
                    name: 'Total',
                    data: [res.all],
                }, {
                    name: 'In Use',
                    data: [res.rented],
                }, {
                    name: 'Available',
                    data: [res.available],
                }];
                const config = {
                    yaxis: {
                        title: {
                            text: 'Number of trailers'
                        },
                    },
                }
                if (!trailersChart) {
                    trailersChart = barChart('trailersChart', series, config);
                } else {
                    trailersChart.updateSeries(series);
                }
            }
        });
    }
    let trucksChart = null;
    const showTrucksChart = () => {
        if (guard !== 'web') {
            return false;
        }
        $.ajax({
            url: '/truck/search',
            type: 'GET',
            data: {
                graph: true,
                shipper,
                trip,
                driver,
            },
            success: (res) => {
                let series = [];
                res.forEach(item => {
                    series.push({
                        name: item.shipper,
                        data: [item.count],
                    })
                });
                const config = {
                    yaxis: {
                        title: {
                            text: 'Number of trailers'
                        },
                    },
                }
                if (!trucksChart) {
                    trucksChart = barChart('trucksChart', series, config);
                } else {
                    trucksChart.updateSeries(series);
                }
            }
        });
    }
    if (guard === 'web') {
        showDriversChart();
        showTrailersChart();
        showTrucksChart();
    }
    /*
     * SELECT2 FILTERS
     */
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
            loadSummary.length = 0;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
            showTrucksChart();
        })
        .on('select2:unselect', (e) => {
            shipper = null;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
            showTrucksChart();
        });
    tripSel.select2({
        ajax: {
            url: '/trip/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    shipper: shipperSel.val(),
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    })
        .on('select2:select', (e) => {
            trip = e.params.data.id;
            loadSummary.length = 0;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
            showTrucksChart();
        })
        .on('select2:unselect', (e) => {
            trip = null;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
            showTrucksChart();
        });
    driverSel.select2({
        ajax: {
            url: '/driver/selection',
            type: 'GET',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                    shipper,
                    trip,
                    driver,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    })
        .on('select2:select', (e) => {
            driver = e.params.data.id;
            loadSummary.length = 0;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
            showTrucksChart();
        })
        .on('select2:unselect', (e) => {
            driver = null;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
            showTrucksChart();
        });
    getLoadsData();
    $(`[id*="_summary"]`).click((e) => {
        e.preventDefault();
        e.stopPropagation();
        const heading = $(e.currentTarget),
            id = heading.attr('id').split('_summary')[0];
        const status = loadSummary.find(obj => obj.status === id);
        if (status)
            showStatusModal(status);
    });

    if (typeof window.Echo !== "undefined")
        window.Echo.private(`load-status-update-${guard}.${loadChannelId}`)
            .listen('LoadUpdate', res => {
                const load = res.load;
                // If the current shipper filter is not the same as the received load, then don't show it
                if (Number(shipperSel.val()) !== Number(load.shipper.id)) {
                    return false;
                }
                const status = load.status;
                let mainIdx = null,
                    dataIdx = null;
                loadSummary.forEach((obj, i) => {
                    //obj.status === load.status
                    const idx = obj.data.findIndex(item => Number(item.id) === Number(load.id));
                    if (idx !== -1) {
                        dataIdx = idx;
                        mainIdx = i;
                        return false;
                    }
                });
                if (dataIdx !== null) {
                    const main = loadSummary[mainIdx];
                    const mainStatus = main.status;
                    if (mainStatus !== status) {
                        main.data.splice(dataIdx, 1);
                        main.count = main.data.length;
                        $(`#${mainStatus}_summary`).text(main.count);
                    } else {
                        return false;
                    }
                }
                const dashCount = $(`#${status}_summary`);
                const summaryToAssign = loadSummary.find(obj => obj.status === status);
                dashCount.text(Number(dashCount.text()) + 1);
                if (summaryToAssign)
                    summaryToAssign.data.push(load);
                else
                    loadSummary.push({
                        status,
                        count: 1,
                        data: [load],
                    });
            });
})();
