/**
 *      ██ ██    ██ ███    ███ ██████
 *      ██ ██    ██ ████  ████ ██   ██
 *      ██ ██    ██ ██ ████ ██ ██████
 * ██   ██ ██    ██ ██  ██  ██ ██
 *  █████   ██████  ██      ██ ██
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @copyright Copyright (c) 2022, Dale Davies
 * @license MIT
 */

import Clock from './Clock';
import EventEmitter from 'eventemitter3';
import Fuse from 'fuse.js';
import Greeting from './Greeting';
import KeyBinds from './KeyBinds';
import SearchSuggestions from './SearchSuggestions';
import Weather from './Weather';

export default class Main {

    constructor() {
        this.latlong = [];
        this.storage = window.localStorage;
        this.clockfrequency = 10000; // 10 seconds.
        this.weatherfrequency = 300000; // 5 minutes.
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
        this.tagsselectorclosebuttonelm = document.querySelector('.tags .close');
        this.showsearchbuttonelm = document.querySelector('.search');
        // If the user has previously asked for geolocation we will have stored the latlong.
        if (this.lastrequestedlocation = this.storage.getItem('lastrequestedlocation')){
            this.latlong = JSON.parse(this.lastrequestedlocation);
        }
        // Finally create instances of the classes we'll be using.
        this.eventemitter = new EventEmitter();
        this.clock = new Clock(this.eventemitter, !!JUMP.ampmclock, !JUMP.owmapikey);
        this.weather = new Weather(this.eventemitter);
        this.keyBinds = new KeyBinds();

        if (this.showsearchbuttonelm) {
            this.searchclosebuttonelm = this.showsearchbuttonelm.querySelector('.close');
            this.fuse = new Fuse(JSON.parse(JUMP.sites), {
                threshold: 0.3,
                keys: ['name', 'tags', 'url']
            });
        }

        // Parse stringsforjs JSON object.
        this.strings = JSON.parse(JUMP.strings)
    }

    /**
     * Get data from OWM and do stuff with it.
     */
    init() {
        // Let's display some images from unsplash then shall we...
        if (JUMP.unsplash) {
            const backgroundelm = document.querySelector('.background');
            if (JUMP.unsplashcolor) {
                backgroundelm.style.backgroundColor = JUMP.unsplashcolor;
            }
            fetch(JUMP.wwwurl + '/api/unsplash/' + JUMP.token + '/')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('JUMP ERROR: There was an issue with the Unsplash API... ' + data.error);
                    return;
                }
                backgroundelm.style.backgroundImage = 'url("' + data.imagedatauri + '")';
                let unsplashlink = document.querySelector('.unsplash a');
                unsplashlink.textContent = data.attribution;
                unsplashlink.href = data.link;
            });
        }

        // If enables then check the status API and update the frontend according to result.
        if (JUMP.checkstatus) {
            fetch(JUMP.wwwurl + '/api/status/' + JUMP.token + '/')
            .then(response => response.json())
            .then(statuses => {
                for (const [id, status] of Object.entries(statuses)) {
                    const siteelm = document.querySelector('#'+id);
                    if (siteelm) {
                        siteelm.classList.add(status);
                        if (status !== 'online') {
                            const sitelinkelm = siteelm.querySelector('a');
                            sitelinkelm.title = '('+this.strings.status.status+': ' + this.strings.status[status] + ') ' + sitelinkelm.title;
                        }
                    }
                }
            });
        }

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
        setInterval(() => {
            this.weather.fetch_owm_data(this.latlong);
        }, this.weatherfrequency);
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

        this.eventemitter.on('weather-error', error => {
            this.eventemitter.emit('show-content');
        });

        this.eventemitter.on('clock-updated', clockdata => {
            if (this.timeelm != null) {
                this.timeelm.innerHTML = clockdata.formatted_time;
            }
            if (this.greetingelm != null) {
                let greeting = new Greeting(clockdata.hour, this.strings);
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

        if (this.showsearchbuttonelm) {
            const searchinput = document.querySelector('.search-form input');
            this.searchsuggestions = new SearchSuggestions(JSON.parse(JUMP.searchengines), searchinput, this.showsearchbuttonelm, this.eventemitter, this.strings);

            // When the search icon is licked, show the search bar and focus on it.
            this.showsearchbuttonelm.addEventListener('click', e => {
                if (!e.target.classList.contains('open')) {
                    this.showsearchbuttonelm.classList.add('open');
                    searchinput.focus();
                }
            });

            // Listen for CTRL+/ key combo and open search bar.
            document.addEventListener('keyup', e => {
                if (e.ctrlKey && e.shiftKey && e.code == 'Slash') {
                    if (!this.showsearchbuttonelm.classList.contains('open')) {
                        this.showsearchbuttonelm.classList.add('open');
                        searchinput.focus();
                    } else {
                        this.search_close();
                    }
                }
            });

            // Handle the close button.
            this.searchclosebuttonelm.addEventListener('click', e => {
                e.stopPropagation();
                this.search_close();
            });

            // Listen for key events triggered by the searh bar and do stuff.
            searchinput.addEventListener('keyup', e => {
                // On arrow down, focus on the first search suggestion.
                let suggestionslist = document.querySelector('.suggestion-list .searchproviders');
                if (e.code === 'ArrowDown') {
                    if (suggestionslist && suggestionslist.childNodes.length) {
                        suggestionslist.firstChild.firstChild.focus();
                    }
                    return;
                }
                // Perform search, limit number of results, create new array containing only what
                // we need and finally display the suggestions on the page.
                let results = [];
                let siteresults = this.fuse.search(searchinput.value);
                siteresults.length = 8;
                if (siteresults.length > 0) {
                    siteresults.forEach((result) => {
                        results.push(result.item);
                    });
                }
                this.searchsuggestions.replace(results);
            });

            // If someone presses enter then open up the first link, this is the default seach engine
            // purely because it is at the top of the list.
            document.querySelector('.search-form').addEventListener('submit', e => {
                e.preventDefault();
                if (searchinput.value != '') {
                    document.querySelector('.searchproviders li a').click();
                }
            });
        }

        this.keyBinds.init();
    }

    search_close() {
        let suggestions = this.showsearchbuttonelm.querySelector('.suggestionholder');
        if (suggestions) {
            suggestions.remove();
        }
        this.showsearchbuttonelm.classList.remove('suggestions');
        document.querySelector('.search').classList.remove('open');
        document.querySelector('.search-form input').value = '';
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
        this.clock.run(this.clockfrequency);
    }

}
