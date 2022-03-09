(() => {
    let barChart = null;
    let rowData = [];
    const dateRange = $('#dateRange');
    const setTotalsRow = () => {
        let income = 0;
        let expenses = 0;
        rowData.forEach(item => {
            income += Number(item.income);
            expenses += Number(item.expenses);
        });
        return [{
            income,
            expenses,
        }];
    }
    const currencyFormatter = (params) => {
        return numeral(params.value).format('$0,0.00');
    }
    const dataTable = new simpleTableAG({
        id: 'dataTable',
        columns: [
            {headerName: "Account", field: "account"},
            {headerName: "Type", field: "type"},
            {headerName: "Quantity", field: "quantity"},
            {headerName: "Income", field: "income", valueFormatter: currencyFormatter},
            {headerName: "Expenses", field: "expenses", valueFormatter: currencyFormatter},
        ],
        rowData,
        gridOptions: {
            components: {
                tableRef: 'dataTable',
            }
        },
        autoHeight: true,
    });
    const initBarChart = (options) => {
        const _options = _.merge({
            chart: {
                height: 350,
                type: 'bar',
            },
            title: {
                text: 'Utility'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'flat',
                    columnWidth: '55%',
                },
            },
            dataLabels: {
                enabled: true,
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            fill: {
                opacity: 1

            },
        }, options);
        if (barChart) {
            barChart.updateOptions(_options);
        } else {
            barChart = new ApexCharts(
                document.querySelector("#barChart"),
                _options
            );
            barChart.render();
        }
    }
    const getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
        $.ajax({
            url: '/report/utilityProjectionData',
            type: 'GET',
            data: {
                start: start.format('YYYY/MM/DD'),
                end: end.format('YYYY/MM/DD'),
            },
            success: (res) => {
                rowData = [];
                res.loadData.carriers.forEach(item => {
                    rowData.push({
                        account: item.name,
                        type: 'Costs of goods sold',
                        quantity: item.quantity,
                        income: item.income,
                        expenses: item.expenses,
                    });
                });
                let incomeSum = 0;
                res.income.forEach(item => {
                    const amount = Number(item.amount);
                    incomeSum += amount;
                    rowData.push({
                        account: item.account ? item.account.name : null,
                        type: item.type ? item.type.name : null,
                        quantity: 1,
                        income: item.amount,
                        expenses: 0,
                    });
                });
                let expenseSum = 0;
                res.expenses.forEach(item => {
                    const amount = Number(item.amount);
                    expenseSum += amount;
                    rowData.push({
                        account: item.account ? item.account.name : null,
                        type: item.type ? item.type.name : null,
                        quantity: 1,
                        income: 0,
                        expenses: item.amount,
                    });
                });
                const incomeTotalSum = incomeSum + res.loadData.paid_income + res.loadData.pending_income;
                const expenseTotalSum = expenseSum + res.loadData.paid_expenses + res.loadData.pending_expenses;
                const utility = incomeTotalSum - expenseTotalSum;
                let series = [
                    {
                        name: 'Income',
                        data: [res.loadData.paid_income, incomeSum, incomeTotalSum],
                    },
                    {
                        name: 'Expenses',
                        data: [-res.loadData.paid_expenses, -expenseSum, -expenseTotalSum],
                    },
                    {
                        name: 'Utility',
                        data: [res.loadData.paid_income - res.loadData.paid_expenses, incomeSum - expenseSum, utility],
                    },
                ];
                let xaxis = {
                    categories: [
                        ['Paid Loads'], ['Accounting'], ['Totals'],
                    ]
                };
                if (!end.isBefore(moment().startOf('week'))) {
                    series[0].data.splice(1, 0, res.loadData.pending_income); // Push to position 1 the pending income data
                    series[1].data.splice(1, 0, -res.loadData.pending_expenses); // Push to position 1 the pending expenses data
                    series[2].data.splice(1, 0, res.loadData.pending_income - res.loadData.pending_expenses); // Push to position 1 the pending calculated utility
                    xaxis.categories.splice(1, 0, ['Pending Loads']); // Push to position 1 the pending section title
                }
                const options = {
                    series,
                    colors: [chartColorsObj.success,chartColorsObj.danger,chartColorsObj.warning],
                    xaxis,
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return numeral(val).format('$0,0.00');
                            }
                        },
                    }
                };
                initBarChart(options);
                dataTable.rowData = rowData;
                dataTable.gridOptions.api.setRowData(rowData);
                dataTable.gridOptions.api.setPinnedBottomRowData(setTotalsRow());
                dataTable.gridOptions.api.sizeColumnsToFit();
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
    dateRange.daterangepicker({
        format: 'YYYY/MM/DD',
        startDate: moment().startOf('week'),
        endDate: moment().endOf('week'),
    }, (start, end, label) => {
        getData(start, end);
    });
    getData();
})();
