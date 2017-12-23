require('./bootstrap');

echo.channel('general')
    .listen('StampEvent', (e) => {
        console.log(e);
    });