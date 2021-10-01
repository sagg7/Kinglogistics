<x-app-layout>

    @section("scripts")
        <script>
            const guard = 'shipper';
            (() => {
                const options = {
                    series: [{
                        name: 'hours',
                        data: [3.2, 3.3, 3.4, 4, 3, 3.8, 3.9, 3.6, 3.1, 5, 7, 3.1, 3.5, 3.9, 3.2, 3.1, 3.9, 3.8]
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                    },
                    forecastDataPoints: {
                        count: 7
                    },
                    stroke: {
                        width: 5,
                        curve: 'smooth'
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: ['10/24/2021', '10/25/2021', '10/26/2021', '10/27/2021', '10/28/2021', '10/29/2021', '10/30/2021', '10/31/2021', '11/1/2021', '11/2/2021', '11/3/2021', '11/4/2021', '11/5/2021', '11/6/2021', '11/7/2021', '11/8/2021', '11/9/2021', '11/10/2021'],
                        tickAmount: 10,
                        labels: {
                            /*formatter: function (value, timestamp, opts) {
                                return opts.dateFormatter(new Date(timestamp), 'dd MMM')
                            }*/
                        }
                    },
                    title: {
                        text: 'Average Load Time',
                        align: 'left',
                        style: {
                            fontSize: "16px",
                            color: '#666'
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            gradientToColors: ['#FDD835'],
                            shadeIntensity: 1,
                            type: 'horizontal',
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100, 100, 100]
                        },
                    },
                    yaxis: {
                        min: 0,
                        max: 20
                    }
                };

                const chart = new ApexCharts(document.querySelector("#time-avg"), options);
                chart.render();
            })();
        </script>
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.5') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection
    @include('dashboard.common.loadStatus')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="time-avg"></div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
