export default class Weather {

    /**
     * Responsible for retrieveing weather data from OWM and doing
     * stuff with it.
     *
     * @param {string} latlong Comma separated string representing a lattitude and longitude.
     */
    constructor(eventemitter) {
        this.eventemitter = eventemitter;
    }

    /**
     * Make an async request to the weather API, parse and return the response.
     */
    fetch_owm_data(latlong) {
        // If we are provided with a latlong then the user must have cliecked on the location
        // button at some point, so let's use this in the api url...
        let apiurl = '/api/weatherdata.php';
        if (latlong.length) {
            apiurl += ('?lat=' + latlong[0] + '&lon=' + latlong[1]);
        }
        // Get some data from the weather api...
        fetch(apiurl)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('JUMP ERROR: There was a problem contacting the OWM API');
                return;
            }
            if (data.cod === 401) {
                console.error('JUMP ERROR: The OWM API key is invalid, check config.php');
                return;
            }
            // Determine if we should use the day or night variant of our weather icon.
            var daynightvariant = 'night';
            if (data.dt > data.sys.sunrise && data.dt < data.sys.sunset) {
                daynightvariant = 'day'
            }
            this.eventemitter.emit('weather-loaded', {
                locationcode: data.id,
                locationname: data.name,
                temp: Math.ceil(data.main.temp) + '&deg;' + (JUMP.metrictemp ? 'C' : 'F'),
                description: data.weather[0].main,
                iconclass: 'wi-owm-' + daynightvariant + '-' + data.weather[0].id,
                timezoneshift: data.timezone*1000,
            });
        })
    }

}
