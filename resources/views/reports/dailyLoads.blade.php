<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Daily Loads</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            let _aggrid
            let lineChart = null;
            (() => {
                const dateRange = $('#dateRange');
                const initChart = (obj) => {
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
                            text: 'Daily finished loads per job',
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
            })();
        </script>
    @endsection

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <fieldset class="form-group col-12">
                        <label for="dateRange">Select Dates</label>
                        <input type="text" id="dateRange" class="form-control">
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
