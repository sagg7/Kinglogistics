class simpleTableAG {
    constructor(properties) {
        this.validationFunction = properties.validationFunction;
        this.pinnedBottomFunction = properties.pinnedBottomFunction ? properties.pinnedBottomFunction : () => {};
        this.onTotalCalc = properties.onTotalCalc;
        this.onRemoveRow = properties.onRemoveRow;
        this.removeValidation = properties.removeValidation;

        this.id = properties.id;
        this.columns = properties.columns;
        this.columnDefs = [];
        this.iva_percentage = properties.iva_percentage ? properties.iva_percentage : 16.00;
        this.constructColumns();
        let columnDefs = this.columnDefs;
        let rowData = this.rowData = properties.rowData ? properties.rowData : [];
        this.gridOptions = {
            columnDefs,
            rowData,
            rowDragManaged: true,
            animateRows: true,
            pagination: false,
            stopEditingWhenGridLosesFocus: true,
            pinnedBottomRowData: this.pinnedBottomFunction(this),
            defaultColDef: {
                flex: 1,
                minWidth: 100,
                sortable: true,
                resizable: true,
            },
            components: {},
            onGridReady: function (params) {
                // Function to resize the table and fit to container width
                params.api.sizeColumnsToFit();

                let rtime,
                    timeout = false,
                    delta = 450;

                // Fit the table width on resize event, after resize ends
                window.addEventListener('resize', function () {
                    rtime = new Date();
                    if (timeout === false) {
                        timeout = true;
                        setTimeout(resizeEnd, delta);
                    }
                });

                function resizeEnd() {
                    if (new Date() - rtime < delta) {
                        setTimeout(resizeEnd, delta);
                    } else {
                        timeout = false;
                        params.api.sizeColumnsToFit();
                    }
                }
                if (properties.autoHeight)
                    params.api.setDomLayout('autoHeight');
            },
            onCellEditingStopped: (event) => {
                // Set the new row data into a variable
                let evData = event.data;
                // Find the index of the row in the global rowData array
                let index = this.rowData.findIndex(obj => {
                    return obj.id === evData.id && obj.type === evData.type;
                });
                if (this.validationFunction) {
                    let val = this.validationFunction(evData);
                    if (!val.validation)
                        this.rowData[index].quantity = val.value;
                }
                // Set the new row total in the rowData array
                this.rowData[index].total = Number(evData.quantity) * Number(evData.price);
                // Save the new data into the table
                this.grid.gridOptions.api.setRowData(this.rowData);
                // Set the new rows for total information
                this.grid.gridOptions.api.setPinnedBottomRowData(this.pinnedBottomFunction(this));
            },
            onRowDragEnd: (event) => {
                // Get rows data with new sequence
                let current = Object.values(event.api.rowRenderer.rowCompsByIndex),
                    newSeqArr = [];
                // Loop for each row data
                current.forEach((item, i) => {
                    let rData = item.rowNode.data, // Set the row data to a variable
                        index = this.rowData.findIndex(obj => { // Find the index of the row in the global rowData array
                            return obj.id === rData.id && obj.type === rData.type;
                        });
                    // Change the sequence in the rowData array
                    //rowData[index].sequence = i;
                    newSeqArr.push(this.rowData[index]);
                });
                this.rowData = newSeqArr;
            }
        };
        if (typeof properties.gridOptions === 'object' && properties.gridOptions !== null)
            _.merge(this.gridOptions, properties.gridOptions);
        this.init();
    }

    constructColumns() {
        this.columns.forEach((item) => {
            this.columnDefs.push(item);
        });
    }

    init() {
        let gridDiv = document.querySelector(`#${this.id}`);
        this.grid = new agGrid.Grid(gridDiv, this.gridOptions);
    }
}
