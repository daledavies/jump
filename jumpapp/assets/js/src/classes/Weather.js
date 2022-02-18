export default class Weather {

    /**
     * Reposible for retrieveing weather data from OWM and doing
     * stuff with it.
     *
     * @param {string} owmapikey OWM API key.
     * @param {string} latlong Comma separated string representing a lattitude and longitude.
     * @param {boolean} metrictemp Are temperature units in metric or imperial.
     */
    constructor(owmapikey, latlong, metrictemp) {
        this.owmapiurlbase = 'https://api.openweathermap.org/data/2.5/weather';
        this.owmapikey = owmapikey;
        this.latlong = latlong;
        this.metrictemp = metrictemp
    }

    /**
     * Make an async request to the OWM API, parse and return the response.
     *
     * @returns {Promise} Containing parsed OWM data.
     */
    async fetch_owm_data() {
        const url = this.owmapiurlbase
                    +'?units=' + (this.metrictemp ? 'metric' : 'imperial')
                    +'&lat=' + this.latlong[0]
                    +'&lon=' + this.latlong[1]
                    +'&appid=' + this.owmapikey;

        // Get some data from the open weather map api...
        const promise = await fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.cod === 401) {
                alert('The OWM API key is invalid, check config.php');
            }
            // Determine if we should use the day or night variant of our weather icon.
            var daynightvariant = 'night';
            if (data.dt > data.sys.sunrise && data.dt < data.sys.sunset) {
                daynightvariant = 'day'
            }
            return {
                locationcode: data.id,
                locationname: data.name,
                temp: Math.ceil(data.main.temp) + '&deg;' + (this.metrictemp ? 'C' : 'F'),
                description: data.weather[0].main,
                iconclass: 'wi-owm-' + daynightvariant + '-' + data.weather[0].id,
                timezoneshift: data.timezone
            };
        })
        return promise;
    }

}
