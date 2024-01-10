import './bootstrap';
// resources/js/app.js

import Echo from 'laravel-echo';

window.io = require('socket.io-client');

const echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001', // Use your Socket.io server address
});

echo.channel('announcements.' + announcementId)
    .listen('CommentPosted', (event) => {
        // Handle the new comment event and update the UI
        console.log('New comment:', event.comment);
    });
