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

window.Echo.channel('driver-location')
    .listen('TruckLocationUpdate', e => {
        console.log(e);
    });


////////////////////////////////////////////////////////////////////////

let onlineUsers = 0;

function update_online_counter() {
    document.getElementById('online').textContent = '' + onlineUsers;
}

window.Echo.channel('chat')
    .listen('NewChatMessage', e => {
        console.log(e);
    });

window.Echo.join('chat')
    .here((users) => {
        onlineUsers = users.length;

        update_online_counter();
    })
    .joining((user) => {
        onlineUsers++;

        update_online_counter();
    })
    .leaving((user) => {
        onlineUsers--;

        update_online_counter();
    });
