window._ = require('lodash');
window.Vue = require('vue').default;
try {
    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */


Vue.prototype.authorize = function(handler) {
    //Additional admin priviliges.

    let user = window.App.user;

    return user ? handler(user) : false;

};

window.axios = require('axios');



window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.App.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'

};

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

window.events = new Vue();

window.flash = function (message, level = 'success') {
    window.events.$emit('flash', { message, level });
};
