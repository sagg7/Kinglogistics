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
            case 'remove':
            case 'reduce':
                if (total - amount < 0) {
                    throwErrorMsg('The total quantity must be bigger than $0');
                    return false;
                }
                if (obj.type === 'increase')
                    reductions += amount;
                else {
                    subtotal -= amount;
                }
                total -= amount;
                break;
            case 'increase':
            case 'add':
                if (obj.type === 'increase')
                    reductions -= amount;
                else
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
                    headerName: "Element Type",
                    field: "objTypeName",
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
            removeValidation: (params) => {
                switch (params.type) {
                    case 'bonus':
                        const bonus = bonuses.find(obj => obj.id === params.id);
                        if (total - bonus.amount < 0) {
                            throwErrorMsg('The total quantity must be bigger than $0');
                            return false;
                        }
                        break;
                    case 'expense':
                        break;
                }
                return true;
            },
            onRemoveRow: (params) => {
                let element = {},
                    id = null;
                switch (params.type) {
                    case 'expense':
                        id = expenses.findIndex(obj => obj.id === params.id);
                        element = expenses[id];
                        if (isNaN(Number(params.id)))
                            expenses.splice(id, 1);
                        calculateTotal({type: 'increase', amount: element.amount});
                        break;
                    case 'bonus':
                        id = bonuses.findIndex(obj => obj.id === params.id);
                        element = bonuses[id];
                        if (!calculateTotal({type: 'remove', amount: element.amount}))
                            return false;
                        if (isNaN(Number(params.id)))
                            bonuses.splice(id, 1);
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
            objType: item.bonust_type_id,
            objTypeName: item.bonus_type ? item.bonus_type.name : '',
            amount: item.amount,
            date: item.date,
            description: item.description,
        });
    });
    expenses.forEach(item => {
        rowData.push({
            id: item.id,
            type: `expense`,
            objType: item.type_id,
            objTypeName: item.type ? item.type.name : '',
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
        bonusTypeCon = $('#bonus-type-container'),
        bonusTypeSel = $('#bonus_type'),
        expenseTypeCon = $('#expense-type-container'),
        expenseTypeSel = $('#expense_type'),
        dateInp = $('[name=date_submit]'),
        descInp = $('#description'),
        amountInp = $('#amount');
    typeSel.select2({
        placeholder: 'Select',
    }).on('select2:select', function (e) {
        switch (e.params.data.id) {
            case 'bonus':
                bonusTypeCon.removeClass('d-none');
                expenseTypeCon.addClass('d-none');
                expenseTypeSel.val('').trigger('change');
                break;
            case 'expense':
                bonusTypeCon.addClass('d-none');
                bonusTypeSel.val('');
                expenseTypeCon.removeClass('d-none');
                break;
        }
    });
    bonusTypeSel.select2();
    expenseTypeSel.select2();
    $('#addElement').click(() => {
        const dateVal = dateInp.val();
        const descVal = descInp.val();
        const amountVal = amountInp.val();
        if (dateVal === '' || descVal === '' || amountVal === '') {
            throwErrorMsg('All fields must be filled to continue');
            return false;
        }
        const type = typeSel.val();
        let obj = {
            id: `new_${Math.random().toString(36).substring(3, 8)}`,
            amount: amountInp.val(),
            date: dateInp.val(),
            description: descInp.val(),
        };
        switch (type) {
            case 'bonus':
                _.merge(obj,{
                    objType: bonusTypeSel.val(),
                    objTypeName: bonusTypeSel.find('option:selected').text(),
                });
                bonuses.push(obj);
                calculateTotal({type: 'add', amount: obj.amount});
                break;
            case 'expense':
                _.merge(obj,{
                    objType: expenseTypeSel.val(),
                    objTypeName: expenseTypeSel.find('option:selected').text(),
                });
                if (!calculateTotal({type: 'reduce', amount: obj.amount}))
                    return false;
                expenses.push(obj);
                break;
        }
        rowData.push(_.merge(obj, {type, date: moment(dateVal).format('MM/DD/YYYY')}));
        fillTable();
        amountInp.val('');
        descInp.val('');
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
