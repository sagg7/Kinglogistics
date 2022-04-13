

    $("#optionToSelectTruckYes").click(function(){
        $("#selectTruckModal").modal('show');
        $("#optionToSelectTruck").modal('hide');
        $('#carrier_id')
        .html(`<option value="${carrierId}">${carrierName}</option>`)
        .val(carrierId)
        .trigger('change')
        .prop('readonly', true);
        $('#trailer_id').html(`<option value=""></option>`).prop('disabled', true);
        $('#seller_id').html(`<option value=""></option>`);
        $('#number').val("");
        $('#plate').val("");
        $('#vin').val("");
        $('#make').val("");
        $('#model').val("");
        $('#year').val("");
        $('#diesel_card').val("");
        $('input[type=checkbox]').prop('checked', false);
    })

    $("#optionToSelectTruckNo").click(function(){
    window.location = '/carrier/index';
    })

        $('#truckForm').submit(e => {
            e.preventDefault();
            const form = $(e.currentTarget);
            const formData = new FormData(form[0]);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: (res) => {
                    // console.log(res.data.id);
                    if (res.success){
                    throwErrorMsg("Truck Generated Successfully", {"title": "Success!", "type": "success"});
                    $("#selectTruckModal").modal('hide');
                     $("#optionToSelectTruck").modal('show');
                    }
                },
                error: () => {
                    throwErrorMsg();
                }
            }).always(() => {
                removeAjaxLoaders();
            });
        });
    




