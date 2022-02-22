let originsAGTable;
let destinationsAGTable;
(() => {
    const loadCountFormatter = (params) => {
        this.eGui = document.createElement('div');
        let count = 0;
        if (params.value)
            params.value.forEach(trip => {
                count += trip.loads_count;
            });
        return count;
    }
    const loadTonsCountFormatter = (params) => {
        this.eGui = document.createElement('div');
        let count = 0;
        if (params.value)
            params.value.forEach(trip => {
                count += trip.loads_tons_sum;
            });
        return count;
    }
    const tripsStatusFormatter = (params) => {
        if (params.value)
            return params.value.charAt(0).toUpperCase()  + params.value.slice(1)
                + ` ${params.data.status_current} of ${params.data.status_total}`;
        else
            return '';
    };
    function CoordsLinkRenderer() {}
    CoordsLinkRenderer.prototype.init = (params) => {
        this.eGui = document.createElement('div');
        const coords = params.value;
        const arr = coords.split(',');
        const latitude = Number(arr[0]).toFixed(5);
        const longitude = Number(arr[1]).toFixed(5);
        this.eGui.innerHTML = `<a href="http://www.google.com/maps/place/${coords}" target="_blank">${latitude},${longitude}</a>`;
    }
    CoordsLinkRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    const tableProperties = (type) => {
        let columns = [
            {headerName: 'Coordinates', field: 'coords', cellRenderer: CoordsLinkRenderer},
            {headerName: 'Name', field: 'name'},
            {headerName: 'Total loads', field: 'trips', filter: false, sortable: false, valueFormatter: loadCountFormatter},
            {headerName: 'Total tons delivered', field: 'trips', filter: false, sortable: false, valueFormatter: loadTonsCountFormatter}
        ];
        switch (type) {
            case 'origins':
                break;
            case 'destinations':
                columns.push({headerName: 'Status', field: 'status', filter: false, sortable: false, valueFormatter: tripsStatusFormatter});
                break;
        }
        return {
            columns,
            container: `${type}Table`,
            tableRef: `${type}AGTable`,
            url: `/trip/${type.slice(0, -1)}/search`,
        }
    };

    const originsModal = $('#viewOriginsModal');
    const destinationsModal = $('#viewDestinationsModal');

    originsModal.on('show.modal.bs', () => {
        if (!originsAGTable)
            originsAGTable = new tableAG(tableProperties('origins'));
    }).on('shown.modal.bs', () => {
        originsAGTable.gridOptions.api.sizeColumnsToFit();
    });
    destinationsModal.on('show.modal.bs', () => {
        if (!destinationsAGTable)
            destinationsAGTable = new tableAG(tableProperties('destinations'));
    }).on('shown.modal.bs', () => {
        destinationsAGTable.gridOptions.api.sizeColumnsToFit();
    });
})();
