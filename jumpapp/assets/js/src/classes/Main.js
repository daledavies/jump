import Clock from './Clock';
import Greeting from './Greeting';
import Weather from './Weather';

export default class Main {

    constructor() {
        this.owmapikey = null;
        this.latlong = [];
        this.storage = window.localStorage;
        this.updatefrequency = 10000;
        this.contentintervalid = null;
        this.timezoneshift = 0;

        // Cache some DOM elements that we will access frequently.
        this.greetingelm = document.querySelector('.greeting .chosen');
        this.holderelm = document.querySelector('.time-weather');
        this.timeelm = this.holderelm.querySelector('.time');
        this.weatherelm = this.holderelm.querySelector('.weather');
        this.clientlocationelm = document.querySelector('.useclientlocation');


        // See if we were provided a latlong and api key via the apps config.php.
        if (JUMP.latlong && JUMP.owmapikey) {
            this.owmapikey = JUMP.owmapikey;
            this.latlong = JUMP.latlong.split(',');
        }

        // If the user has previously asked for geolocation we will have stored the latlong.
        if (this.lastrequestedlocation = this.storage.getItem('lastrequestedlocation')){
            this.latlong = JSON.parse(this.lastrequestedlocation);
        }
    }

    /**
     * Get data from OWM and do stuff with it.
     */
    init() {
        // If there is no OWM API key provided then just update the greeting
        // and clock, otherwise we can go get the weather data and set everything
        // up properly.
        if (!this.owmapikey) {
            this.refresh_basic_content();
            this.show_content();
            return;
        }

        // Retrieve weather and timezone data from Open Weather Map API.
        new Weather(this.owmapikey, this.latlong).fetch_owm_data().then(owmdata => {

            // Update the timezone shift from UTC to whatever it should be for the
            // requested location, then tell the greeting and clock to update.
            this.timezoneshift = owmdata.timezoneshift;
            this.refresh_basic_content();

            // Display the weather icon, link to the requested location in OWM
            // and update location name element.
            this.holderelm.href += 'city/' + owmdata.locationcode;
            this.weatherelm.classList.add(owmdata.iconclass);
            this.clientlocationelm.innerHTML = owmdata.locationname;

            // Should someone click on the location button then request their location
            // from the client and store it, then re run init() to update the page.
            this.clientlocationelm.addEventListener('click', e => {
                navigator.geolocation.getCurrentPosition(position => {
                    this.latlong = [position.coords.latitude, position.coords.longitude];
                    this.storage.setItem('lastrequestedlocation', JSON.stringify(this.latlong));
                    this.init();
                }, null, {enableHighAccuracy: true});
            }, {once: true});
            this.clientlocationelm.classList.add('enable');

            // Finally we can make everything visible.
            this.show_content();
        });

    }

    /**
     * Once everything is set up we can remove the .hidden class to display content
     * on the page to stop things jumping around between the initial page load
     * and JS rendering.
     */
    show_content() {
        document.querySelectorAll('.hidden').forEach(function(element){
            element.classList.remove('hidden');
        });
    }

    /**
     * Calculate the correct time for the requested location and display it,
     * along with an appropriate greeting.
     */
    update_basic_content() {
        let clock = new Clock(this.timezoneshift);
        let greeting = new Greeting(clock);
        this.timeelm.innerHTML = clock.get_formatted_time();
        this.greetingelm.innerHTML = greeting.get_greeting();
    }

    /**
     * Update the greeting message and clock initially, then continue to update
     * them at the frequency set in this.updatefrequency.
     */
    refresh_basic_content() {
        // Clear any previously set intervals for updating content.
        if (this.contentintervalid) {
            clearInterval(this.contentintervalid);
        }

        // Set the clock and greeting text appropriately for the requested location.
        this.update_basic_content();

        // Update the content periodically, we don't need to be too frequent as we are
        // not displaying seconds on the clock.
        this.contentintervalid = setInterval(() => {
            this.update_basic_content();
        }, this.updatefrequency);
    }

}
