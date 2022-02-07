/**
 * Do some fancy UI stuff in a rather unfancy way.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

if (JUMP.latlong && JUMP.owmapikey) {
    var latlong = JUMP.latlong.split(',');
    // Get some data from the open weather map api...
    fetch('https://api.openweathermap.org/data/2.5/weather?lat='+latlong[0]+'&lon='+latlong[1]+'&appid='+JUMP.owmapikey)
    .then(function(resp) {
        // Attempt to convert the response into a json object.
        return resp.json();
    })
    .then(function(data) {
        // Determine if we should use the ay or night variant of our weather icon.
        var datnightvariant = 'night';
        if (data.dt > data.sys.sunrise && data.dt < data.sys.sunset) {
            datnightvariant = 'day'
        }
        // Link to the correct city in openweathermap and display the appropriate weather icon.
        var holderelm = document.querySelector('.time-weather');
        holderelm.href += 'city/' + data.sys.id;
        holderelm.querySelector('.weather').classList.add('wi-owm-'+datnightvariant+'-'+data.weather[0].id);
    });
}




