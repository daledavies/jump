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

    constructor(hour, strings) {
        this.hour = hour;
        this.greetings = {
            0  : strings.greetings.goodmorning,
            12 : strings.greetings.goodafternoon,
            16 : strings.greetings.goodevening,
            19 : strings.greetings.goodnight
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
