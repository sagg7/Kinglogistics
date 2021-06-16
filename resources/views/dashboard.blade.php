<x-app-layout>

    @section("scripts")
    <script>
        (() => {
            $.ajax({
                url: '/dashboard/getData',
                type: 'GET',
                success: (res) => {
                    if (res.loads) {
                        Object.entries(res.loads).forEach(item => {
                            const [key, value] = item;
                            const countUp = new CountUp(`${key}_summary`, value);
                            !countUp.error ? countUp.start() : console.error(countUp.error);
                        });
                    }
                },
                error: () => {
                    throwErrorMsg();
                }
            })
        })();
    </script>
    @endsection

    <section id="loads-summary">
        <div class="card bg-analytics">
            <div class="card-content">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-rgba-info p-50 m-0 mb-1">
                        <div class="avatar-content text-white">
                            <i class="fas fa-truck-loading text-info font-large-1"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 class="mb-2">Loads Status</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th><h6>Unallocated</h6></th>
                                <th><h6>Requested</h6></th>
                                <th><h6>Accepted</h6></th>
                                <th><h6>Loading</h6></th>
                                <th><h6>To&nbsp;location</h6></th>
                                <th><h6>Arrived</h6></th>
                                <th><h6>Unloading</h6></th>
                                <th><h6>Finished</h6></th>
                            </tr>
                            <tbody>
                            <tr>
                                <td><h2 id="unallocated_summary">0</h2></td>
                                <td><h2 id="requested_summary">0</h2></td>
                                <td><h2 id="accepted_summary">0</h2></td>
                                <td><h2 id="loading_summary">0</h2></td>
                                <td><h2 id="to_location_summary">0</h2></td>
                                <td><h2 id="arrived_summary">0</h2></td>
                                <td><h2 id="unloading_summary">0</h2></td>
                                <td><h2 id="finished_summary">0</h2></td>
                            </tr>
                            </tbody>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>
