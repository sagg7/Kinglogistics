const loadSummary = [];
(() => {
    $.ajax({
        url: '/dashboard/getData',
        type: 'GET',
        success: (res) => {
            if (res.loads) {
                Object.entries(res.loads).forEach(item => {
                    const [key, value] = item;
                    const countUp = new CountUp(`${key}_summary`, value);
                    !countUp.error ? countUp.start() : console.error(countUp.error);
                    loadSummary.push({
                        status: key,
                        count: value,
                    })
                });
            }
        },
        error: () => {
            throwErrorMsg();
        }
    });
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
            html += `<tr><td>${item.shipper.name}</td><td>${item.origin}</td><td>${item.destination}</td><td>${item.driver.name}</td><td>${item.truck.number}</td>` +
                (guard !== 'carrier' ? `<td>${item.driver.carrier.name}</td></tr>` : '');
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
            if (!status.data)
                $.ajax({
                    url: '/dashboard/loadSummary',
                    data: {
                        status: id,
                    },
                    success: (res) => {
                        status.data = res;
                        showStatusModal(status);
                    },
                    error: () => {
                        throwErrorMsg();
                    }
                });
            else
                showStatusModal(status);
    });
})();
