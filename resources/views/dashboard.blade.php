<x-app-layout>

    @section("scripts")
    <script>
        const loadSummary = [];
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
                            loadSummary.push({
                                status: key,
                                count: value,
                            })
                        });
                    }
                },
                error: () => {
                    throwErrorMsg();
                }
            });
            const capitalizeString = (string) => {
                return string.charAt(0).toUpperCase() + string.slice(1);
            };
            const showStatusModal = (status) => {
                const modal = $('#viewLoadStatus'),
                    modalTitle = modal.find('.modal-title'),
                    modalBody = modal.find('.modal-body'),
                    modalSpinner = modalBody.find('.modal-spinner'),
                    modalContent = modalBody.find('.content-body');
                modalSpinner.removeClass('d-none');
                modalContent.addClass('d-none');
                modalContent.html('<div class="table-responsive"><table class="table table-hover" id="loadTableSummary"><thead><tr>' +
                    '<th>Shipper</th>' +
                    '<th>Origin</th>' +
                    '<th>Destination</th>' +
                    '<th>Driver</th>' +
                    '<th>Truck#</th>' +
                    '<th>Carrier</th>' +
                    '</tr></thead><tbody></tbody></table></div>');
                const loadTable = $('#loadTableSummary'),
                    tbody = loadTable.find('tbody');
                modalTitle.text(`${capitalizeString(status.status)}`);
                let html = '';
                status.data.forEach(item => {
                    html += `<tr><td>${item.shipper.name}</td><td>${item.origin}</td><td>${item.destination}</td><td>${item.driver.name}</td><td>${item.truck.number}</td><td>${item.driver.carrier.name}</td></tr>`;
                });
                tbody.html(html);
                modalSpinner.addClass('d-none');
                modalContent.removeClass('d-none');
                modal.modal('show');
            };
            $(`[id*="_summary"]`).click((e) => {
                e.preventDefault();
                e.stopPropagation();
                const heading = $(e.currentTarget),
                    id = heading.attr('id').split('_')[0];
                const status = loadSummary.find(obj => obj.status === id);
                if (status)
                    if (!status.data)
                        $.ajax({
                            url: '/dashboard/loadSummary',
                            data: {
                                status: id,
                            },
                            success: (res) => {
                                status.data = res;
                                showStatusModal(status);
                            },
                            error: () => {
                                throwErrorMsg();
                            }
                        });
                    else
                        showStatusModal(status);
            });
        })();
    </script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
    @endsection

    <section id="loads-summary">
        <div class="card bg-analytics">
            <div class="card-content">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-rgba-info p-50 m-0 mb-1">
                        <div class="avatar-content text-white">
                            <i class="fas fa-truck-loading font-large-1"></i>
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
                                <th><h6>In&nbsp;transit</h6></th>
                                <th><h6>Arrived</h6></th>
                                <th><h6>Unloading</h6></th>
                                <th><h6>Finished</h6></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="#"><h2 id="unallocated_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="requested_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="accepted_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="loading_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="to_location_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="arrived_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="unloading_summary">0</h2></a></td>
                                <td><a href="#"><h2 id="finished_summary">0</h2></a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>
