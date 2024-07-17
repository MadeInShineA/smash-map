/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.withCredentials = true
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.interceptors.request.use((config) => {
    const accessToken = localStorage.getItem('accessToken');

    if (accessToken) {
      config.headers['Authorization'] = `Bearer ${accessToken}`;
    }

    return config;
})

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';
//
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
//
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: window.location.hostname,
//     wsPort: import.meta.env.VITE_PUSHER_PORT,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     disableStats: true,
//     forceTLS: false,
//     enabledTransports: ['ws', 'wss'],
//     authorizer: (channel, options) => {
//         return{
//             authorize(socketId, callback){
//                 (axios)({
//                     method: "POST",
//                     url: import.meta.env.VITE_ECHO_APP_URL + "/broadcasting/auth",
//                     data: {
//                         socket_id: socketId,
//                         channel_name: channel.name
//                     }
//                 })
//                     .then((response) =>{
//                         callback(false, response.data)
//                     })
//                     .catch((error => {
//                         console.log(error)
//                         callback(true, error)
//                     }))
//             }
//         }
//     }
// });

