// laravel echo
import Echo from 'laravel-echo';

window.echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001',
    auth:
        {
            headers:
                {
                    'Authorization': 'Bearer ' + 'af17c8556c099c6d'
                }
        }
});
