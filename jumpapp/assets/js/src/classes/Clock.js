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
     * @param boolean ampm Return 12 hour format if true.
     * @param number utcshift Number of seconds to shift time from UTC.
     */
    constructor(eventemitter, ampm = false) {
        this.set_utc_shift();
        this.contentintervalid = null;
        this.eventemitter = eventemitter;
        this.ampm = ampm;
    }

    set_utc_shift(newutcshift = 0) {
        this.utcshift = newutcshift;
        this.shiftedtimestamp = new Date().getTime()+this.utcshift;
        this.shifteddate = new Date(this.shiftedtimestamp);
    }

    /**
     * Return a formatted string representing time for display in template.
     *
     * @returns string The time string.
     */
    get_formatted_time() {
        // We need to use getUTCHours and getUTC Minutes here to stop
        // the Date() object adjusting the returned time relative to the
        // browser's local timezone.
        let hour = this.shifteddate.getUTCHours();
        const minutes = String(this.shifteddate.getUTCMinutes()).padStart(2, '0');

        if (!this.ampm) {
            return String(hour).padStart(2, '0') + ":" + minutes;
        }
        // Convert to 12 hour AM/PM format and return.
        const suffix = hour <= 12 ? 'AM':'PM';
        hour = ((hour + 11) % 12 + 1);
        return hour + ':' + minutes + '<span>' + suffix + '</span>';
    }

    /**
     * Returns just the hour.
     *
     * @returns number The hour.
     */
    get_hour() {
        return this.shifteddate.getUTCHours();
    }

    update_time() {
        this.set_utc_shift(this.utcshift);
        this.eventemitter.emit('clock-updated', {
            formatted_time: this.get_formatted_time(),
            hour: this.get_hour(),
            utcshift: this.utcshift
        });
    }

    run(updatefrequency) {
        // Clear any previously set intervals for updating content.
        if (this.contentintervalid) {
            clearInterval(this.contentintervalid);
        }
        // Set the clock and greeting text appropriately for the requested location.
        this.update_time();
        // Update the content periodically, we don't need to be too frequent as we are
        // not displaying seconds on the clock.
        this.contentintervalid = setInterval(() => {
            this.update_time();
        }, updatefrequency);
    }

}
