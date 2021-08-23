(() => {
    let rowData = [],
        barChart = null;
    function MenuRenderer() {}
    MenuRenderer.prototype.init = (params) => {
        console.log(params.data);
        this.eGui = document.createElement('div');
        if (params.data.id) {
            this.eGui.innerHTML = `<a href="/carrier/payment/downloadPDF/${params.data.id}" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a>`;
        }
    }
    MenuRenderer.prototype.getGui = () => {
        return this.eGui;
    }
    const dateRange = $('#dateRange'),
        currencyFormatter = (params) => {
            // If not a number, return value unchanged
            if (isNaN(params.value))
                return params.value
            else // else returned the formatted value
                return numeral(params.value).format('$0,0.00');
        },
        initChart = (data) => {
            const barSeries = [{
                name: 'Subtotal',
                data: [data.subtotal.toFixed(2)],
            }, {
                name: 'Reductions',
                data: [data.reductions.toFixed(2)],
            }, {
                name: 'Total',
                data: [data.total.toFixed(2)],
            }];
            if (barChart) {
                barChart.updateSeries(barSeries);
                return;
            }
            // Column Chart
            // ----------------------------------
            let options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                colors: [chartColorsObj.success, chartColorsObj.danger, chartColorsObj.primary],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        endingShape: 'flat',
                        columnWidth: '55%',
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: barSeries,
                legend: {
                    offsetY: -10
                },
                xaxis: {
                    categories: [''],
                },
                yaxis: {
                    title: {
                        text: '$ (summary)'
                    },
                },
                fill: {
                    opacity: 1

                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return numeral(val).format('$0,0.00');
                        }
                    }
                }
            }
            barChart = new ApexCharts(
                document.querySelector("#chart"),
                options
            );
            barChart.render();
        },
        fillTable = () => {
            if (_aggrid) {
                _aggrid.rowData = rowData;
                _aggrid.gridOptions.api.setRowData(rowData);
                _aggrid.grid.gridOptions.api.setPinnedBottomRowData(_aggrid.pinnedBottomFunction(_aggrid));
                _aggrid.gridOptions.api.sizeColumnsToFit();
                return;
            }
            _aggrid = new simpleTableAG({
                id: 'reportTable',
                columns: [
                    {
                        headerName: "Date",
                        field: "date",
                    },
                    {
                        headerName: "Subtotal",
                        field: "subtotal",
                        valueFormatter: currencyFormatter,
                    },
                    {
                        headerName: "Reductions",
                        field: "reductions",
                        valueFormatter: currencyFormatter,
                    },
                    {
                        headerName: "Total",
                        field: "total",
                        valueFormatter: currencyFormatter,
                    },
                    {
                        headerName: "",
                        field: "menu",
                        cellRenderer: MenuRenderer,
                    },
                ],
                gridOptions: {
                    components: {
                        tableRef: '_aggrid',
                        MenuRenderer: MenuRenderer,
                    },
                },
                pinnedBottomFunction: (params) => {
                    let subtotal = 0,
                        reductions = 0,
                        total = 0;
                    params.rowData.forEach((item) => {
                        subtotal += Number(item.subtotal);
                        reductions += Number(item.reductions);
                        total += Number(item.total);
                    });
                    return [{
                        date: '',
                        subtotal,
                        reductions,
                        total,
                    }];
                },
                autoHeight: true,
                rowData,
            });
            setTimeout(() => {
                _aggrid.gridOptions.api.sizeColumnsToFit();
            }, 300);
        },
        getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
            $.ajax({
                url: '/report/historicalData',
                type: 'GET',
                data: {
                    start: start.format('YYYY/MM/DD'),
                    end: end.format('YYYY/MM/DD'),
                },
                success: (res) => {
                    rowData = [];
                    let subtotal = 0,
                        reductions = 0,
                        total = 0;
                    res.forEach(item => {
                        rowData.push({
                            id: item.id,
                            date: item.date,
                            subtotal: item.gross_amount,
                            reductions: item.reductions,
                            total: item.total,
                        });
                        subtotal += Number(item.gross_amount);
                        reductions += Number(item.reductions);
                        total += Number(item.total);
                    });
                    initChart({
                        subtotal,
                        reductions,
                        total,
                    });
                    fillTable();
                }
            })
        };
    dateRange.daterangepicker({
        format: 'YYYY/MM/DD',
        locale: dateRangeLocale,
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
    }, (start, end, label) => {
        getData(start, end);
    });
    getData();
})();
