function RemoveBtnRenderer() {}
RemoveBtnRenderer.prototype.deleteFunction = (grid, id, type) => {
    _.pull(window[grid].rowData, _.find(window[grid].rowData, {id, type}));
    // Save the new data into the table
    window[grid].gridOptions.api.setRowData(window[grid].rowData);
    // Set the new rows for total information
    window[grid].gridOptions.api.setPinnedBottomRowData(window[grid].pinnedBottomFunction(window[grid]));

    if (window[grid].onRemoveRow)
        window[grid].onRemoveRow();
}
RemoveBtnRenderer.prototype.init = (params) => {
    if (params.value !== false) {
        this.eGui = document.createElement('span');
        this.eGui.className = 'd-inline-block';
        this.eGui.innerHTML = `<button type="button" class="btn btn-link btn-remove"><i class="feather icon-x"></i></button>`;

        $(this.eGui).click(function (e) {
            e.preventDefault();
            RemoveBtnRenderer.prototype.deleteFunction(params.api.gridCore.gridOptions.components.tableRef, params.data.id, params.data.type);
        });
    } else {
        this.eGui = null;
    }
}
RemoveBtnRenderer.prototype.getGui = () => {
    return this.eGui;
}

function IvaCheckRenderer() {}
IvaCheckRenderer.prototype.toggleIva = (grid, api, checked) => {
    grid.enableIva = checked;
    api.setPinnedBottomRowData(grid.pinnedBottomFunction(grid));
}
IvaCheckRenderer.prototype.init = (params) => {
    let tableDef = params.api.gridCore.gridOptions.components.tableRef,
        grid = window[tableDef];
    if (params.data.renderIva) {
        this.eGui = document.createElement('fieldset');
        this.eGui.className = 'form-group pull-right';
        this.eGui.innerHTML = `<div class="vs-checkbox-con vs-checkbox-primary">` +
            `<input type="checkbox" value="true" id="ivaCheck" ${grid.enableIva ? 'checked' : ''}>` +
            '<span class="vs-checkbox">' +
            '<span class="vs-checkbox--check">' +
            '<i class="vs-icon feather icon-check"></i>' +
            '</span>' +
            '</span>' +
            `<label for="ivaCheck">${params.value}</label>` +
            '</div>';

        $(this.eGui).find('input').click(function (e) {
            IvaCheckRenderer.prototype.toggleIva(grid, params.api, $(e.currentTarget).is(':checked'));
        });
    } else {
        this.eGui = document.createElement('span');
        this.eGui.innerHTML = params.value;
    }
}
IvaCheckRenderer.prototype.getGui = () => {
    return this.eGui;
}

function DiscountInputRenderer() {}
DiscountInputRenderer.prototype.calculateDiscount = (grid, api, val) => {
    val = Number(val);
    grid.rowData.forEach((item, i) => {
        grid.rowData[i].discount = val;
        let subtotal = Number(item.quantity) * Number(item.price);
        grid.rowData[i].total = subtotal - Number(val * subtotal / 100).toFixed(2);
    });
    grid.generalDiscount = val;
    // set new data with discount
    api.setRowData(grid.rowData);
    api.setPinnedBottomRowData(grid.pinnedBottomFunction(grid));
}
DiscountInputRenderer.prototype.init = (params) => {
    let tableDef = params.api.gridCore.gridOptions.components.tableRef,
        grid = window[tableDef];
    if (params.data.renderDiscInp) {
        this.eGui = document.createElement('div');
        this.eGui.className = "d-flex align-items-center";
        this.eGui.innerHTML = `<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">%</span></div><input type="text" class="form-control" value="${grid.generalDiscount}"></div>` +
            `<span class="">&nbsp;= $${params.value}</span>`;

        let valCalc = (inp) => {
            let val = Number(inp.val());
            if (isNaN(val)) {
                inp.val(0);
                return false;
            } else if (val > 100) {
                val = 100;
                inp.val(val);
            }
            DiscountInputRenderer.prototype.calculateDiscount(grid, params.api, val);
        };
        $(this.eGui).find('input').focus((e) => {
            let inp = $(e.currentTarget),
                val = Number(inp.val());
            if (val === 0)
                inp.val('');
        }).focusout((e) => {
            let inp = $(e.currentTarget);
            valCalc(inp);
        });
    } else {
        this.eGui = document.createElement('span');
        this.eGui.innerHTML = numeral(params.value).format('$0,0.00');
    }
}
DiscountInputRenderer.prototype.getGui = () => {
    return this.eGui;
}

class AgGridFront {
    constructor(properties) {
        this.validationFunction = properties.validationFunction;
        this.pinnedBottomFunction = properties.pinnedBottomFunction ? properties.pinnedBottomFunction : this.totalRows;
        this.onTotalCalc = properties.onTotalCalc;
        this.onRemoveRow = properties.onRemoveRow;
        this.enableIva = false;

        this.id = properties.id;
        this.columns = properties.columns;
        this.columnDefs = [];
        this.iva_percentage = properties.iva_percentage || properties.iva_percentage === 0 ? properties.iva_percentage : 16.00;
        if (this.iva_percentage !== 0)
            this.enableIva = true;
        this.hasDiscount = properties.hasDiscount ? properties.hasDiscount : false;
        this.generalDiscount = properties.generalDiscount ? properties.generalDiscount : 0;
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
            /*localeText: {
                noRowsToShow: 'No hay elementos para mostrar',
            },*/
            defaultColDef: {
                flex: 1,
                minWidth: 100,
                sortable: true,
                resizable: true,
            },
            components: {
                RemoveBtnRenderer: RemoveBtnRenderer,
                IvaCheckRenderer: IvaCheckRenderer,
                DiscountInputRenderer: DiscountInputRenderer,
            },
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
                if (event.colDef.field === "discount") {
                    let discount = 0;
                    if (Number(event.value) > 100)
                        discount = 100;
                    else if (!isNaN(event.value))
                        discount = Number(event.value);
                    this.rowData[index].discount = discount;
                    this.generalDiscount = 0;
                }
                // Set the new row total in the rowData array
                let subtotal = Number(evData.quantity) * Number(evData.price);
                if (this.hasDiscount)
                    this.rowData[index].total = subtotal - Number((Number(this.rowData[index].discount) * subtotal / 100).toFixed(2));
                else
                    this.rowData[index].total = subtotal;
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
            },
            /*onRowDataChanged: (event) => {

            }*/
        };
        if (typeof properties.gridOptions === 'object' && properties.gridOptions !== null)
            _.merge(this.gridOptions, properties.gridOptions);
        this.init();
    }

    constructColumns() {
        this.columns.forEach((item, i) => {
            if (this.hasDiscount ? i === 3 : i === 2) {
                item.cellRenderer = "IvaCheckRenderer";
                item.cellStyle = function (params) {
                    if (!params.data.renderIva && isNaN(params.value))
                        return {pointerEvents: 'none'};
                }
            } else if (this.hasDiscount) {
                if (i === 4)
                    item.cellRenderer = "DiscountInputRenderer";
            } else {
                item.cellStyle = function (params) {
                    if (!params.value) {
                        return {pointerEvents: 'none'};
                    }
                }
            }
            if (item.field === "discount") {
                item.cellRenderer = this.calculateDiscount;
            }
            this.columnDefs.push(item);
        });
        this.columnDefs.push({
            headerName: "",
            field: "remove",
            cellRenderer: 'RemoveBtnRenderer',
            width: 70,
            maxWidth: 70,
            minWidth: 70,
            cellStyle: {padding: 0,},
        });
    }

    calculateDiscount(params) {
        if (isNaN(params.value))
            return params.value;
        let subtotal = Number(params.data.quantity) * Number(params.data.price);
        return numeral(Number(params.value) * subtotal / 100).format('$0,0.00');
    }

    totalRows(grid) { // Set the final row that calculates Subtotal, IVA and Total values
        let subtotal = 0,
            discount = 0,
            iva = 0,
            total = 0;

        grid.rowData.forEach((item) => {
            let sub = Number(item.quantity) * Number(item.price);

            if (item.discount)
                discount += Number((Number(item.discount) * sub / 100).toFixed(2));

            subtotal += sub;
        });

        total += subtotal - discount;

        if (grid.enableIva)
            iva += total * (grid.iva_percentage / 100);

        if (grid.onTotalCalc)
            grid.onTotalCalc({total, iva});

        let row = [],
            obj;
        obj = {
            description: '',
            quantity: '',
            price: 'Subtotal',
            total: subtotal,
            remove: false,
        };
        if (grid.hasDiscount) {
            obj.price = '';
            obj.discount = 'Subtotal';
        }
        row.push(obj);
        if (grid.iva_percentage !== 0) {
            obj = {
                description: '',
                quantity: '',
                price: 'IVA',
                total: iva,
                remove: false,
                renderIva: true,
                checkedIva: grid.enableIva,
            };
            if (grid.hasDiscount) {
                obj.price = '';
                obj.discount = 'IVA';
            }
            row.push(obj);
        }
        if (discount > 0 || grid.hasDiscount) {
            obj = {
                description: '',
                quantity: '',
                price: '',
                discount: '',
                total: discount,
                remove: false,
                renderDiscInp: true,
            }
            if (grid.hasDiscount)
                obj.discount = 'Descuento';
            else
                obj.price = 'Descuento';
            row.push(obj);
        }
        obj = {
            description: '',
            quantity: '',
            price: 'Total',
            total: total + iva,
            remove: false,
        };
        if (grid.hasDiscount) {
            obj.price = '';
            obj.discount = 'Total';
        }
        row.push(obj);
        return row;
    }

    init() {
        let gridDiv = document.querySelector(`#${this.id}`);
        this.grid = new agGrid.Grid(gridDiv, this.gridOptions);

        $('.modern-nav-toggle').click(() => {
            setTimeout(() => {
                this.grid.gridOptions.api.sizeColumnsToFit();
            }, 500);
        });
    }
}
