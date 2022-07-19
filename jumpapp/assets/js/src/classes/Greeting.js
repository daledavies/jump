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

import Clock from "./Clock";

export default class Greeting {

    constructor(hour) {
        this.hour = hour;
        this.greetings = {
            0  : 'morning',
            12 : 'afternoon',
            16 : 'evening',
            19 : 'night'
        };
    }

    get_greeting() {
        let keys = Object.keys(this.greetings).reverse();
        for (let element of keys) {
            if (this.hour >= element) {
                return this.greetings[element];
            }
        };
    }

}
