(() => {
    let rowData = [];
    const subtotalInp = $('#subtotal'),
        reductionsInp = $('#reductions'),
        totalInp = $('#total');
    let subtotal = Number(subtotalInp.val()),
        reductions = Number(reductionsInp.val()),
        total = Number(totalInp.val());
    const calculateTotal = (obj) => {
        const amount = Number(obj.amount);
        switch (obj.type) {
            case 'reduce':
                if (total - amount < 0)
                    return false;
                reductions += amount;
                total -= amount;
                break;
            case 'add':
                subtotal += amount;
                total += amount;
                break;
        }
        subtotalInp.val(numeralFormat(subtotal));
        reductionsInp.val(numeralFormat(reductions));
        totalInp.val(numeralFormat(total));
        return true;
    };
    const numeralFormat = (value) => {
        return numeral(value).format('$0,0.00')
    };
    const moneyFormatter = (params) => {
        if (params.value)
            return numeral(params.value).format('$0,0.00');
        else
            return '$0.00';
    };
    const capitalizeFormatter = (params) => {
        if (params.value)
            return params.value.charAt(0).toUpperCase()  + params.value.slice(1);
        else
            return '';
    };
    const fillTable = () => {
        if (_aggrid) {
            _aggrid.rowData = rowData;
            _aggrid.gridOptions.api.setRowData(rowData);
            _aggrid.grid.gridOptions.api.setPinnedBottomRowData(_aggrid.pinnedBottomFunction(_aggrid));
            _aggrid.gridOptions.api.sizeColumnsToFit();
            return;
        }
        _aggrid = new simpleTableAG({
            id: 'paymentData',
            columns: [
                {
                    headerName: "Date",
                    field: "date",
                },
                {
                    headerName: "Type",
                    field: "type",
                    valueFormatter: capitalizeFormatter,
                },
                {
                    headerName: "Description",
                    field: "description",
                },
                {
                    headerName: "Amount",
                    field: "amount",
                    valueFormatter: moneyFormatter,
                },
                {
                    headerName: "",
                    field: "remove",
                    cellRenderer: 'RemoveBtnRenderer',
                    width: 70,
                    maxWidth: 70,
                    minWidth: 70,
                    cellStyle: {padding: 0,},
                }
            ],
            gridOptions: {
                components: {
                    RemoveBtnRenderer: RemoveBtnRenderer,
                    tableRef: '_aggrid',
                },
            },
            autoHeight: true,
            rowData,
            onRemoveRow: (params) => {
                let element = {},
                    id = null;
                switch (params.type) {
                    case 'expense':
                        id = expenses.findIndex(obj => obj.id === params.id);
                        element = expenses[id];
                        if (isNaN(Number(params.id)))
                            expenses.splice(id, 1);
                        calculateTotal({type: 'add', amount: element.amount});
                        break;
                    case 'bonus':
                        id = bonuses.findIndex(obj => obj.id === params.id);
                        element = bonuses[id];
                        if (isNaN(Number(params.id)))
                            bonuses.splice(id, 1);
                        calculateTotal({type: 'reduce', amount: element.amount});
                        break;
                    default:
                        return;
                }
                element.delete = 1;
            }
        });
        setTimeout(() => {
            _aggrid.gridOptions.api.sizeColumnsToFit();
        }, 300);
    };
    bonuses.forEach(item => {
        rowData.push({
            id: item.id,
            type: `bonus`,
            amount: item.amount,
            date: item.date,
            description: item.description,
        });
    });
    expenses.forEach(item => {
        rowData.push({
            id: item.id,
            type: `expense`,
            amount: item.amount,
            date: item.date ? item.date : item.created_at,
            description: item.description,
        });
    });
    fillTable();
    subtotalInp.val(numeralFormat(subtotalInp.val()));
    reductionsInp.val(numeralFormat(reductionsInp.val()));
    totalInp.val(numeralFormat(totalInp.val()));
    const typeSel = $('#type'),
        dateInp = $('[name=date_submit]'),
        descInp = $('#description'),
        amountInp = $('#amount');
    typeSel.select2({
        placeholder: 'Select',
    });
    $('#addElement').click(() => {
        const type = typeSel.val();
        let obj = {
            id: `new_${Math.random().toString(36).substring(3, 8)}`,
            amount: amountInp.val(),
            date: dateInp.val(),
            description: descInp.val(),
        };
        rowData.push(_.merge(obj, {type}));
        fillTable();
        switch (type) {
            case 'bonus':
                bonuses.push(obj);
                calculateTotal({type: 'add', amount: obj.amount});
                break;
            case 'expense':
                expenses.push(obj);
                calculateTotal({type: 'reduce', amount: obj.amount});
                break;
        }
        amountInp.val('');
        descInp.val('');
        typeSel.val('').trigger('change');
    });
    $('#updatePayment').submit((e) => {
        e.preventDefault();
        const form = $(e.currentTarget),
            url = form.attr('action');
        $.ajax({
            url,
            type: 'POST',
            data: {
                bonuses,
                expenses,
                subtotal,
                reductions,
                total,
            },
            success: (res) => {
                if (res.success)
                    window.location = '/carrier/payment';
                else
                    throwErrorMsg();
            },
            error: () => {
                throwErrorMsg();
            }
        })
    });
})();
