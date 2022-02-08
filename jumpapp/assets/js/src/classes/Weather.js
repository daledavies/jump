export default class Weather {

    constructor(owmapikey, latlong) {
        this.owmapiurlbase = 'https://api.openweathermap.org/data/2.5/weather';
        this.owmapikey = owmapikey;
        this.latlong = latlong;
    }

    async fetch_owm_data() {
        const url = this.owmapiurlbase
                    +'?lat='+this.latlong[0]
                    +'&lon='+this.latlong[1]
                    +'&appid='+this.owmapikey;

        // Get some data from the open weather map api...
        const promise = await fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.cod === 401) {
                alert('The OWM API key is invalid, check config.php');
            }
            // Determine if we should use the ay or night variant of our weather icon.
            var datnightvariant = 'night';
            if (data.dt > data.sys.sunrise && data.dt < data.sys.sunset) {
                datnightvariant = 'day'
            }
            return {
                locationcode: data.id,
                locationname: data.name,
                iconclass: 'wi-owm-'+datnightvariant+'-'+data.weather[0].id,
                timezoneshift: data.timezone
            };
        })
        return promise;
    }

}
