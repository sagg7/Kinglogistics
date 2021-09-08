(() => {
    const tripSel = $('[name=trips]');
    let trip = null;
    const loadSummary = [];
    const summaryArea = $('#loads-summary');
    const summaryTable = summaryArea.find('table');
    const getLoadsData = () => {
        summaryTable.find('h2').text(0);
        $.ajax({
            url: '/dashboard/getData',
            type: 'GET',
            data: {
                trip,
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
                    });
                }
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
    tripSel.select2({
        ajax: {
            url: '/trip/selection',
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
            trip = e.params.data.id;
            loadSummary.length = 0;
            getLoadsData();
        })
        .on('select2:unselect', (e) => {
            trip = null;
            getLoadsData();
        });
    getLoadsData();
    const capitalizeString = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1);
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
            '<th>Shipper</th>' +
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
        status.data.forEach(item => {
            html += `<tr><td>${item.shipper.name}</td><td>${item.origin}</td><td>${item.destination}</td>` +
                `<td>${item.driver ? item.driver.name : ''}</td><td>${item.truck ? item.truck.number : ''}</td>` +
                (guard !== 'carrier' ? `<td>${item.driver ? item.driver.carrier.name : ''}</td></tr>` : '');
        });
        tbody.html(html);
        modalSpinner.addClass('d-none');
        modalContent.removeClass('d-none');
        modal.modal('show');
    };
    $(`[id*="_summary"]`).click((e) => {
        e.preventDefault();
        e.stopPropagation();
        const heading = $(e.currentTarget),
            id = heading.attr('id').split('_')[0];
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
