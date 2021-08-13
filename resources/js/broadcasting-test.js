import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: "22be9655aa323fd39490",
    encrypted: false,
    cluster: "us2",
    forceTLS: true,
    disableStats: true,
});

document.getElementById('joinChannelAsShippers').addEventListener('click', () => {
    joinToShipperssChannel()
})

function joinToShipperssChannel() {
    console.log('Joining as shippers')
    window.Echo.private('driver-location-shipper.1')
        .listen('DriverLocationUpdateForShipper', e => {
            console.log('Shipper broadcasted event', e);
        });
}

document.getElementById('joinChannelAsCarrier').addEventListener('click', () => {
    joinToCarriersChannel()
})

function joinToCarriersChannel() {
    console.log('Joining as carriers')
    window.Echo.private('driver-location-carrier.2')
        .listen('DriverLocationUpdateForCarrier', e => {
            console.log('Carrier broadcasted event', e);
        });
}

document.getElementById('joinAdminChannel').addEventListener('click', () => {
    joinToAdminChannel()
})

function joinToAdminChannel() {
    console.log('Joining as admin')
    window.Echo.private('driver-location-king')
        .listen('DriverLocationUpdateForKing', e => {
            console.log('Admin broadcasted event', e);
        });
}

////////////////////////////////////////////////////////////////////////

let onlineUsers = 0;

function update_online_counter() {
    document.getElementById('online').textContent = '' + onlineUsers;
}

window.Echo.private('chat')
    .listen('NewChatMessage', e => {
        console.log("New message", e);
    });
