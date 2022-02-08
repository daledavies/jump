export default class Clock {
    constructor(utcshift = 0) {
        this.utcshift = utcshift*1000;
        this.shiftedtimestamp = new Date().getTime()+this.utcshift;
        this.shifteddate = new Date(this.shiftedtimestamp);
    }

    get_formatted_time() {
        const hour = String(this.shifteddate.getHours()).padStart(2, "0");
        const minutes = String(this.shifteddate.getMinutes()).padStart(2, "0");
        return hour + ":" + minutes;
    }

    get_hour() {
        return this.shifteddate.getHours();
    }

}
