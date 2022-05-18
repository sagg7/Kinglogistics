<div class="modal fade" id="loadDetails" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center p-5 modal-spinner">
                    <span class="spinner-border" role="status" aria-hidden="true"></span>
                </div>
                <div class="content-body d-none">
                    <div class="row">
                        <div class="col-md-6">
                            <span id="route_string"></span>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td><strong>Age</strong></td>
                                    <td id="load_details_age" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Trip miles</strong></td>
                                    <td id="load_details_mileage" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Trailer type</strong></td>
                                    <td id="load_details_trailer" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Load size</strong></td>
                                    <td id="load_details_size" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Length</strong></td>
                                    <td id="load_details_length" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Weight</strong></td>
                                    <td id="load_details_weight" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Width</strong></td>
                                    <td id="load_details_width" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Height</strong></td>
                                    <td id="load_details_height" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Payrate</strong></td>
                                    <td id="load_details_payrate" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Ship date</strong></td>
                                    <td id="load_details_date" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Delivery date</strong></td>
                                    <td id="load_details_delivery" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Shipper's comments</strong></td>
                                    <td id="load_details_comments" class="text-right"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td><strong>Shipper</strong></td>
                                    <td id="shipper_name" class="text-right"></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone</strong></td>
                                    <td id="shipper_phone" class="text-right"></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="row align-items-end">
                                <div class="form-group col-lg-6">
                                    <label for="requestTruck">Truck for request</label>
                                    <select id="requestTruck" name="request_truck" class="form-control"></select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <button type="button" class="btn btn-primary btn-block submit-ajax" id="requestLoad">Request</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
