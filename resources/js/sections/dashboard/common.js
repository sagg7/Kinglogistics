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
    const activeDrivers = [];
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
                        if (driverSel.is(':empty')) {
                            value.data.forEach((item) => {
                                const driver = item.driver ? activeDrivers.find(obj => Number(obj.id) === Number(item.driver.id)) : null;
                                if (!driver && driver !== null)
                                    activeDrivers.push({
                                        id: item.driver.id,
                                        name: item.driver.name,
                                    });
                            });
                        }
                    });
                    if (driverSel.is(':empty')) {
                        activeDrivers.sort((a,b) => (a.id > b.id) ? 1 : ((b.id > a.id) ? -1 : 0))
                        let html = '<option></option>';
                        activeDrivers.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        driverSel.html(html);
                    }
                }
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
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
        })
        .on('select2:unselect', (e) => {
            shipper = null;
            getLoadsData();
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
        })
        .on('select2:unselect', (e) => {
            trip = null;
            getLoadsData();
        });
    driverSel.select2({
        placeholder: 'Select',
        allowClear: true,
    })
        .on('select2:select', (e) => {
            driver = e.params.data.id;
            loadSummary.length = 0;
            getLoadsData();
        })
        .on('select2:unselect', (e) => {
            driver = null;
            getLoadsData();
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
