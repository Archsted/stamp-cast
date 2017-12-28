require('./bootstrap');
require('./echo');

echo.channel('general')
    .listen('StampEvent', (e) => {
        console.log(e);
    });