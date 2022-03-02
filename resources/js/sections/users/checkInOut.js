// let checkInId = 0;
let checkOutId = $('#checkOutId');
let checkInId = $('#checkInId');
function getCheckInModal() {
    let getCheckInModal = $('#getCheckInModal');
    getCheckInModal.modal('show');
    const bodyToCheckIn = getCheckInModal.find('.modal-body');
    const buttonToCheckIn = getCheckInModal.find('.modal-footer');

    buttonToCheckIn.empty();
    bodyToCheckIn.empty();


    bodyToCheckIn.append(
        `<div class="text-center">` +
        ` <button type="button" class="btn btn-success btn-circle btn-xl" onclick="submitCheckIn()" data-dismiss="modal">Check In</button>` +
        `</div>`);

}

function showPosition(position) {
    console.log("Latitude: " + position.coords.latitude +
        "<br>Longitude: " + position.coords.longitude);
}

function submitCheckIn() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position){
            $.ajax({
                url: `/user/storeCheckIn`,
                type: 'POST',
                data: {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                },
                success: (res) => {
                    throwErrorMsg("Report Generated correctly", {"title": "Success!", "type": "success"});
                    checkId = res.data.id;
                    $('#checkOutId').show();
                    $('#checkInId').hide();

                },
                error: (res) => {

                    throwErrorMsg('You cannot check in if you already have an open session');
                },
            });
        });
    } else {
        // Browser doesn't support Geolocation
        throwErrorMsg("Browser doesn't support Geolocation");
    }
}




function getCheckOutModal(id) {
    let getCheckInModal = $('#getCheckOutModal');
    getCheckInModal.modal('show');
    const bodyToCheckIn = getCheckInModal.find('.modal-body');
    const buttonToCheckIn = getCheckInModal.find('.modal-footer');

    buttonToCheckIn.empty();
    bodyToCheckIn.empty();



    bodyToCheckIn.append(
        `<div class="text-center">` +
        ` <button type="button" class="btn btn-danger btn-circle btn-xl" onclick="submitCheckOut()" data-dismiss="modal">Check Out</button>`+
        `</div>`);
}


function submitCheckOut() {
    // alert(checkId);
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
                $.ajax({
                    url: `/user/storeCheckOut/${checkId}`,
                    type: 'POST',
                    data: {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    },
                    success: (res) => {
                        throwErrorMsg("Report Generated correctly", {"title": "Success!", "type": "success"});
                        checkInId.show();
                        checkOutId.hide();
                    },
                    error: (res) => {

                        throwErrorMsg('You cannot check out if you dont have open session');
                    },
                });
            }
        );
    } else {
        // Browser doesn't support Geolocation
        throwErrorMsg("Browser doesn't support Geolocation");
    }
}


(() => {
// alert(checkId);

if(checkId!=0){
    checkOutId.show();
    checkInId.hide();


}else if(checkId==0){
    checkInId.show();
    checkOutId.hide();}
})();



