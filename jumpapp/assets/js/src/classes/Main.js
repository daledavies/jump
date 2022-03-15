import Clock from './Clock';
import EventEmitter from 'eventemitter3';
import Greeting from './Greeting';
import Weather from './Weather';

export default class Main {

    constructor() {
        this.latlong = [];
        this.storage = window.localStorage;
        this.updatefrequency = 10000;
        this.timezoneshift = 0;
        this.metrictemp = JUMP.metrictemp;
        // Cache some DOM elements that we will access frequently.
        this.greetingelm = document.querySelector('.greeting .chosen');
        this.holderelm = document.querySelector('.time-weather');
        this.tempelm = this.holderelm.querySelector('.weather-info .temp');
        this.weatherdescelm = this.holderelm.querySelector('.weather-info .desc');
        this.timeelm = this.holderelm.querySelector('.time');
        this.weatherelm = this.holderelm.querySelector('.weather');
        this.weathericonelm = this.holderelm.querySelector('.weather-icon');
        this.clientlocationelm = document.querySelector('.useclientlocation');
        this.showtagsbuttonelm = document.querySelector('.show-tags');
        this.tagselectorelm = document.querySelector('.tags');
        this.tagsselectorclosebuttonelm = document.querySelector('.tags .close')
        // If the user has previously asked for geolocation we will have stored the latlong.
        if (this.lastrequestedlocation = this.storage.getItem('lastrequestedlocation')){
            this.latlong = JSON.parse(this.lastrequestedlocation);
        }
        // Finally create instances of the classes we'll be using.
        this.eventemitter = new EventEmitter();
        this.clock = new Clock(this.eventemitter);
        this.weather = new Weather(this.eventemitter);
    }

    /**
     * Get data from OWM and do stuff with it.
     */
    init() {
        // Start listening for events so we can do stuff when needed.
        this.add_event_listeners();
        // If there is no OWM API key provided then just update the greeting
        // and clock, otherwise we can go get the weather data and set everything
        // up properly.
        if (!JUMP.owmapikey) {
            this.eventemitter.emit('show-content');
            return;
        }
        // Retrieve weather and timezone data from Open Weather Map API.
        this.weather.fetch_owm_data(this.latlong);
    }

    /**
     * Umm... adds event listeners
     */
    add_event_listeners() {
        this.eventemitter.on('weather-loaded', owmdata => {
            // Update the timezone shift from UTC to whatever it should be for the
            // requested location, then tell the greeting and clock to update.
            this.timezoneshift = owmdata.timezoneshift;
            // Display the weather icon, link to the requested location in OWM
            // and update location name element.
            this.weatherelm.href = 'https://openweathermap.org/city/' + owmdata.locationcode;
            this.weathericonelm.classList.add(owmdata.iconclass);
            this.clientlocationelm.innerHTML = owmdata.locationname;
            this.tempelm.innerHTML = owmdata.temp;
            this.weatherdescelm.innerHTML = owmdata.description;
            this.clientlocationelm.classList.add('enable');
            this.eventemitter.emit('show-content');
        });

        this.eventemitter.on('clock-updated', clockdata => {
            if (this.timeelm != null) {
                this.timeelm.innerHTML = clockdata.formatted_time;
            }
            if (this.greetingelm != null) {
                let greeting = new Greeting(clockdata.hour);
                this.greetingelm.innerHTML = greeting.get_greeting();
            }
        });

        this.eventemitter.on('show-content', () => {
            this.set_clock();
            this.show_content();
        });

        // Should someone click on the location button then request their location
        // from the client and store it, then refetch weather data to update the page.
        this.clientlocationelm.addEventListener('click', e => {
            navigator.geolocation.getCurrentPosition(position => {
                this.latlong = [position.coords.latitude, position.coords.longitude];
                this.storage.setItem('lastrequestedlocation', JSON.stringify(this.latlong));
                this.weather.fetch_owm_data(this.latlong);
            },
            error => {
                console.error(error.message);
            },
            {enableHighAccuracy: true});
        });

        if (this.showtagsbuttonelm) {
            this.showtagsbuttonelm.addEventListener('click', e => {
                this.tagselectorelm.classList.add('enable');
                e.preventDefault();
            });
        }

        if (this.tagsselectorclosebuttonelm) {
            this.tagsselectorclosebuttonelm.addEventListener('click', e => {
                this.tagselectorelm.classList.remove('enable');
            });
        }

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

    set_clock() {
        this.clock.set_utc_shift(this.timezoneshift);
        this.clock.run(this.updatefrequency);
    }

}
