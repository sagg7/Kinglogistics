import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    //key: "22be9655aa323fd39490",
    key: "95a1f607e45c48c22795",
    encrypted: false,
    cluster: "us2",
    forceTLS: true,
    disableStats: true,
});
