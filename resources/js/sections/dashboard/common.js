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
            return `${params.value}%`;
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
                + `${params.data.status_current} of ${params.data.status_total}`;
        else
            return '';
    };
    const minutesAVGFormatter = (params) => {
        if (params.value)
            return `${params.value} min`;
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
                    name: 'Pending',
                    data: [res.morning.pending, res.night.pending]
                }, {
                    name: 'Error',
                    data: [res.morning.error, res.night.error]
                }]
                if (!driversChart) {
                    const config = {
                        colors: [chartColorsObj.success, chartColorsObj.info, chartColorsObj.warning, chartColorsObj.danger],
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
                    data: [res.available],
                }, {
                    name: 'Available',
                    data: [res.rented],
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
    if (guard === 'web') {
        showDriversChart();
        showTrailersChart();
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
        })
        .on('select2:unselect', (e) => {
            shipper = null;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
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
        })
        .on('select2:unselect', (e) => {
            trip = null;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
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
        })
        .on('select2:unselect', (e) => {
            driver = null;
            getLoadsData();
            showDriversChart();
            showTrailersChart();
        });
    getLoadsData();
    const capitalizeString = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };
    const showLoadModal = (data) => {
        const modal = $('#viewLoad'),
            //modalTitle = modal.find('.modal-title'),
            modalBody = modal.find('.modal-body'),
            modalSpinner = modalBody.find('.modal-spinner'),
            modalContent = modalBody.find('.content-body');
        modalSpinner.removeClass('d-none');
        modalContent.addClass('d-none');
        modalContent.html('<div class="table-responsive">' +
            '<table class="table">' +
            '<thead>' +
            '<tr>' +
            (guard !== 'shipper' ? `<th>Shipper</th>` : '') +
            '<th>Driver</th>' +
            '<th>Truck#</th>' +
            (guard !== 'carrier' ? `<th>Carrier</th>` : '<th></th>') + (guard === 'shipper' ? `<th></th>` : '') +
            '</tr>' +
            '</thead>' +
            '<tbody>' +
            '<tr>' +
            (guard !== 'shipper' ? `<td>${data.shipper.name}</td>` : '') +
            `<td>${data.driver ? data.driver.name : ''}</td>` +
            `<td>${data.truck ? data.truck.number : ''}</td>` +
            (guard !== 'carrier' ? `<td>${data.driver ? data.driver.carrier.name : ''}</td>` : '<td></td>') +
            '</tr>' +
            '<tr>' +
            '<th>Load type</th>' +
            '<th>Date</th>' +
            '<th>Origin</th>' +
            '<th>Destination</th>' +
            '</tr>' +
            '<tr>' +
            `<td>${data.load_type ? data.load_type.name : ''}</td>` +
            `<td>${data.date ? data.date : ''}</td>` +
            `<td>${data.origin ? data.origin : ''}</td>` +
            `<td>${data.destination ? data.destination : ''}</td>` +
            '</tr>' +
            '<tr>' +
            '<th>Control#</th>' +
            '<th>Customer name</th>' +
            '<th>Customer po</th>' +
            '<th>Customer reference</th>' +
            '</tr>' +
            '<tr>' +
            `<td>${data.control_number ? data.control_number : ''}</td>` +
            `<td>${data.customer_name ? data.customer_name : ''}</td>` +
            `<td>${data.customer_po ? data.customer_po : ''}</td>` +
            `<td>${data.customer_reference ? data.customer_reference : ''}</td>` +
            '</tr>' +
            '<tr>' +
            '<th>Weight</th>' +
            '<th>Tons</th>' +
            '<th>Silo number</th>' +
            '<th>Mileage</th>' +
            '</tr>' +
            '<tr>' +
            `<td>${data.weight ? data.weight : ''}</td>` +
            `<td>${data.tons ? data.tons : ''}</td>` +
            `<td>${data.silo_number ? data.silo_number : ''}</td>` +
            `<td>${data.mileage ? data.mileage : ''}</td>` +
            '</tr>' +
            '</tbody>' +
            '</table>' +
            '</div>'
        );
            modalSpinner.addClass('d-none');
        modalContent.removeClass('d-none');
        modal.modal('show');
    };
    const showStatusModal = (status) => {
        const modal = $('#viewLoadStatus'),
            modalTitle = modal.find('.modal-title'),
            modalBody = modal.find('.modal-body'),
            modalSpinner = modalBody.find('.modal-spinner'),
            modalContent = modalBody.find('.content-body');
        modalSpinner.removeClass('d-none');
        modalContent.addClass('d-none');
        modalContent.html('<div class="table-responsive"><table class="table table-hover" id="loadTableSummary"><thead><tr>' +
            (guard !== 'shipper' ? `<th>Shipper</th>` : '') +
            '<th>Origin</th>' +
            '<th>Destination</th>' +
            '<th>Driver</th>' +
            '<th>Truck#</th>' +
            (guard !== 'carrier' ? '<th>Carrier</th>' : '') +
            '</tr></thead><tbody></tbody></table></div>');
        const loadTable = $('#loadTableSummary'),
            tbody = loadTable.find('tbody');
        modalTitle.text(`${capitalizeString(status.status)}`);
        let html = '';
        const idArr = [];
        status.data.forEach((item, i) => {
            const id = `row_${i}`;
            idArr.push(id);
            html += `<tr class="cursor-pointer" id="${id}">` +
                (guard !== 'shipper' ? `<td>${item.shipper.name}</td>` : '') +
                `<td>${item.origin}</td>` +
                `<td>${item.destination}</td>` +
                `<td>${item.driver ? item.driver.name : ''}</td>` +
                `<td>${item.truck ? item.truck.number : ''}</td>` +
                (guard !== 'carrier' ? `<td>${item.driver ? item.driver.carrier.name : ''}</td></tr>` : '');
        });
        tbody.html(html);
        idArr.forEach((id, i) => {
            const element = $(`#${id}`);
            element.click(() => {
                const data = status.data[i];
                showLoadModal(data);
            });
        });
        modalSpinner.addClass('d-none');
        modalContent.removeClass('d-none');
        modal.modal('show');
    };
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
        window.Echo.private('load-status-update')
            .listen('LoadUpdate', res => {
                const load = res.load;
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
