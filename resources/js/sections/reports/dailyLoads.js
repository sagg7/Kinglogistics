(() => {
    let lineChart = null;
    const dateRange = $('#dateRange');
    const graphType = $('#graphType');
    const shipperSel = $('#shipper_id');
    const initChart = (obj) => {
        let titleText;
        switch (graphType.val()) {
            default:
            case 'trips':
                titleText = 'Daily finished loads per job';
                break;
            case 'shippers':
                titleText = 'Daily finished loads per customer';
                break;
        }
        // Line Chart
        // ----------------------------------
        const options = {
            series: obj.series,
            chart: {
                height: 350,
                type: 'line',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 18,
                    left: 7,
                    blur: 10,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                }
            },
            //colors: ['#77B6EA', '#545454'],
            dataLabels: {
                enabled: true,
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: titleText,
                align: 'left'
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            markers: {
                size: 1
            },
            xaxis: {
                categories: obj.categories,
                title: {
                    text: 'Day'
                }
            },
            yaxis: {
                title: {
                    text: 'Loads'
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        };
        if (lineChart) {
            lineChart.updateOptions(options);
            return;
        }
        lineChart = new ApexCharts(
            document.querySelector("#chart"),
            options
        );
        lineChart.render();
    }, getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
        $.ajax({
            url: '/report/dailyLoadsData',
            type: 'GET',
            data: {
                start: start.format('YYYY/MM/DD'),
                end: end.format('YYYY/MM/DD'),
                graph_type: graphType.val(),
                shipper: shipperSel.val(),
            },
            success: (res) => {
                initChart(res);
            }
        });
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

    graphType.select2()
        .on('select2:select', () => {
            getData();
        });
    shipperSel.select2({
        ajax: {
            url: '/shipper/selection',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        },
        placeholder: 'Select',
        allowClear: true,
    })
        .on('select2:select', () => {
            getData();
        })
        .on('select2:unselect', () => {
            getData();
        });
})();
