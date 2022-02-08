import Clock from "./Clock";

export default class Greeting {

    constructor(clock) {
        this.hour = clock.get_hour();
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
