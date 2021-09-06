<x-app-layout>
    <x-slot name="crumb_section">Reports</x-slot>
    <x-slot name="crumb_subsection">Trips</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js') }}"></script>
        <script>
            (() => {
                const dateRange = $('#dateRange'),
                    trip = $('[name=trips]');
                let barChart = null;
                const initChart = (series, xaxis) => {
                        if (barChart) {
                            barChart.updateSeries(series);
                            return;
                        }
                        // Column Chart
                        // ----------------------------------
                        let options = {
                            chart: {
                                height: 350,
                                type: 'bar',
                            },
                            colors: [chartColorsObj.success, chartColorsObj.primary],
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
                            series,
                            legend: {
                                offsetY: -10
                            },
                            xaxis,
                            yaxis: {
                                title: {
                                    text: 'Number of loads'
                                },
                            },
                            fill: {
                                opacity: 1

                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return Number(val);
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
                    getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
                        $.ajax({
                            url: '/report/tripsData',
                            type: 'GET',
                            data: {
                                start: start.format('YYYY/MM/DD'),
                                end: end.format('YYYY/MM/DD'),
                                trip: trip.val(),
                            },
                            success: (res) => {
                                let series = [],
                                    xaxis = {categories: []};
                                res.forEach((item, i) => {
                                    item.loads.forEach(load => {
                                        const serItem = series.find(obj => obj.name === load.load_type.name);
                                        if (serItem) {
                                            if (serItem.data[i])
                                                serItem.data[i]++;
                                            else
                                                serItem.data[i] = 1;
                                        } else
                                            series.push({
                                                name: load.load_type.name,
                                                data: [1],
                                            });
                                    });
                                    xaxis.categories.push(item.name);
                                });
                                let sel2Data = [''];
                                xaxis.categories.forEach((item, i) => {
                                    sel2Data.push({id: i, text: item});
                                });
                                initChart(series, xaxis);
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
                trip.select2({
                    placeholder: 'Select',
                    allowClear: true,
                }).on('select2:select', () => {
                    getData();
                }).on('select2:unselect', () => {
                    getData();
                });
                getData();
            })();
        </script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <fieldset class="form-group">
                        <label for="dateRange">Select Dates</label>
                        <input type="text" id="dateRange" class="form-control">
                    </fieldset>
                </div>
                <div class="col-6">
                    <fieldset class="form-group">
                        <label for="trips">Trip</label>
                        {!! Form::select('trips', $trips, null, ['class' => 'form-control']) !!}
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div id="chart"></div>
            </div>
        </div>
    </div>
</x-app-layout>
