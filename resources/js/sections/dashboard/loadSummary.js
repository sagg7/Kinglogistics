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
        (guard !== 'shipper' ? `<th>Customer</th>` : '') +
        '<th>Driver</th>' +
        '<th>Truck#</th>' +
        (guard === 'web' ? `<th>Carrier</th>` : '<th></th>') + (guard === 'shipper' ? `<th></th>` : '') +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        '<tr>' +
        (guard !== 'shipper' ? `<td>${data.shipper.name}</td>` : '') +
        `<td>${data.driver ? data.driver.name : ''}</td>` +
        `<td>${data.truck ? data.truck.number : ''}</td>` +
        (guard === 'carrier' ? `<td>${data.driver ? data.driver.carrier.name : ''}</td>` : '<td></td>') +
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
        '<th>Customer PO</th>' +
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
        `<th>${session['tons'] ?? 'Tons'}</th>` +
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
    const capitalizeString = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };
    const modal = $('#viewLoadStatus'),
        modalTitle = modal.find('.modal-title'),
        modalBody = modal.find('.modal-body'),
        modalSpinner = modalBody.find('.modal-spinner'),
        modalContent = modalBody.find('.content-body');
    modalSpinner.removeClass('d-none');
    modalContent.addClass('d-none');
    modalContent.html('<div class="table-responsive"><table class="table table-hover" id="loadTableSummary"><thead><tr>' +
        (guard !== 'shipper' ? `<th>Customer</th>` : '') +
        '<th>Origin</th>' +
        '<th>Destination</th>' +
        '<th>Driver</th>' +
        '<th>Truck#</th>' +
        (guard === 'carrier' ? '<th>Carrier</th>' : '') +
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
            (guard === 'carrier' ? `<td>${item.driver ? item.driver.carrier.name : ''}</td></tr>` : '');
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
