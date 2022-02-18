/**
 * Calculate the time, local to the requested location from
 * the OpenWeather API, by passing in the number of seconds
 * that location has shifted from UTC based on the timezones.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

export default class Clock {
    /**
     * Calculate the time shifted from UTC.
     *
     * @param number utcshift Number of seconds to shift time from UTC.
     */
    constructor(utcshift = 0) {
        this.utcshift = utcshift*1000;
        this.shiftedtimestamp = new Date().getTime()+this.utcshift;
        this.shifteddate = new Date(this.shiftedtimestamp);
    }

    /**
     * Return a string representing time in HH:MM format.
     *
     * @returns string The time string.
     */
    get_formatted_time() {
        // We need to use getUTCHours and getUTC Minutes here to stop
        // the Date() object adjusting the returned time relative to the
        // browser's local timezone.
        const hour = String(this.shifteddate.getUTCHours()).padStart(2, "0");
        const minutes = String(this.shifteddate.getUTCMinutes()).padStart(2, "0");
        return hour + ":" + minutes;
    }

    /**
     * Returns just the hour.
     *
     * @returns number The hour.
     */
    get_hour() {
        return this.shifteddate.getUTCHours();
    }

}
