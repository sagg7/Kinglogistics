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
            {headerName: "Revenue", field: "income", valueFormatter: currencyFormatter},
            {headerName: "COGS", field: "expenses", valueFormatter: currencyFormatter},
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
    const pieIncome = {
        chart: null,
        id: "pieIncome",
    };
    const pieExpense = {
        chart: null,
        id: "pieExpenses",
    };
    const initPieChart = (chartData, options) => {
        const _options = _.merge({
            chart: {
                type: 'pie',
                height: options.series.length > 0 ? 280 : 0,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return numeral(val).format('$0,0.00');
                    }
                },
            },
        }, options);
        if (chartData.chart) {
            chartData.chart.updateOptions(_options);
        } else {
            chartData.chart = new ApexCharts(
                document.querySelector(`#${chartData.id}`),
                _options
            );
            chartData.chart.render();
        }
    }
    const getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
        $.ajax({
            url: '/report/profitAndLossData',
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
                let incomeData = [];
                let incomeLabels = [];
                let incomeUndefined = 0;
                res.income.forEach(item => {
                    const amount = Number(item.amount);
                    incomeSum += amount;
                    if (item.type) {
                        const indexOf = incomeLabels.indexOf(item.type.name);
                        if (indexOf === -1) {
                            incomeData.push(amount);
                            incomeLabels.push(item.type.name);
                        } else {
                            incomeData[indexOf] += amount;
                        }
                    } else {
                        incomeUndefined += amount;
                    }
                    rowData.push({
                        account: item.account ? item.account.name : null,
                        type: item.type ? item.type.name : null,
                        quantity: 1,
                        income: item.amount,
                        expenses: 0,
                    });
                });
                if (incomeUndefined > 0) {
                    incomeLabels.push('undefined');
                    incomeData.push(incomeUndefined);
                }
                let expenseSum = 0;
                let expenseData = [];
                let expenseLabels = [];
                let expenseUndefined = 0;
                res.expenses.forEach(item => {
                    const amount = Number(item.amount);
                    expenseSum += amount;
                    if (item.type) {
                        const indexOf = expenseLabels.indexOf(item.type.name);
                        if (indexOf === -1) {
                            expenseData.push(Number(amount.toFixed(2)));
                            expenseLabels.push(item.type.name);
                        } else {
                            expenseData[indexOf] = Number((expenseData[indexOf] + amount).toFixed(2));
                        }
                    } else {
                        expenseUndefined += amount;
                    }
                    rowData.push({
                        account: item.account ? item.account.name : null,
                        type: item.type ? item.type.name : null,
                        quantity: 1,
                        income: 0,
                        expenses: item.amount,
                    });
                });
                if (expenseUndefined > 0) {
                    expenseLabels.push('undefined');
                    expenseData.push(expenseUndefined);
                }
                const incomeTotalSum = Number((incomeSum + res.loadData.paid_income + res.loadData.pending_income).toFixed(2));
                const expenseTotalSum = Number((expenseSum + res.loadData.paid_expenses + res.loadData.pending_expenses).toFixed(2));
                const utility = Number((incomeTotalSum - expenseTotalSum).toFixed(2));
                incomeSum = Number((incomeSum).toFixed(2))
                expenseSum = Number((expenseSum).toFixed(2));
                let series = [
                    {
                        name: 'Revenue',
                        data: [Number(res.loadData.paid_income.toFixed(2)), incomeSum, incomeTotalSum],
                    },
                    {
                        name: 'COGS',
                        data: [Number(-res.loadData.paid_expenses.toFixed(2)), -expenseSum, -expenseTotalSum],
                    },
                    {
                        name: 'Gross Profit',
                        data: [Number((res.loadData.paid_income - res.loadData.paid_expenses).toFixed(2)), Number((incomeSum - expenseSum).toFixed(2)), utility],
                    },
                ];
                let xaxis = {
                    categories: [
                        ['Paid Loads'], ['Accounting'], ['Totals'],
                    ]
                };
                if (!end.isBefore(moment().startOf('week'))) {
                    series[0].data.splice(1, 0, Number((res.loadData.pending_income).toFixed(2))); // Push to position 1 the pending income data
                    series[1].data.splice(1, 0, Number((-res.loadData.pending_expenses).toFixed(2))); // Push to position 1 the pending expenses data
                    series[2].data.splice(1, 0, Number((res.loadData.pending_income - res.loadData.pending_expenses).toFixed(2))); // Push to position 1 the pending calculated utility
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
                    },
                    title: {
                        text: 'Gross Profit'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                position: 'top', // top, center, bottom
                            },
                        }
                    },
                };
                initBarChart(options);
                initPieChart(pieIncome, {
                    series: incomeData,
                    labels: incomeLabels,
                    title: {text:'Income'},
                });
                initPieChart(pieExpense, {
                    series: expenseData,
                    labels: expenseLabels,
                    title: {text:'Expenses'},
                });
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
