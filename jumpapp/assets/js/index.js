/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************!*\
  !*** ./jumpapp/assets/js/src/index.js ***!
  \****************************************/
/**
 * Do some fancy UI stuff in a rather unfancy way.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

if (JUMP.latlong && JUMP.owmapikey) {
    var latlong = JUMP.latlong.split(',');
    // Get some data from the open weather map api...
    fetch('https://api.openweathermap.org/data/2.5/weather?lat='+latlong[0]+'&lon='+latlong[1]+'&appid='+JUMP.owmapikey)
    .then(function(resp) {
        // Attempt to convert the response into a json object.
        return resp.json();
    })
    .then(function(data) {
        // Determine if we should use the ay or night variant of our weather icon.
        var datnightvariant = 'night';
        if (data.dt > data.sys.sunrise && data.dt < data.sys.sunset) {
            datnightvariant = 'day'
        }
        // Link to the correct city in openweathermap and display the appropriate weather icon.
        var holderelm = document.querySelector('.time-weather');
        holderelm.href += 'city/' + data.sys.id;
        holderelm.querySelector('.weather').classList.add('wi-owm-'+datnightvariant+'-'+data.weather[0].id);
    });
}





/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5kZXguanMiLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0w7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0wiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9qdW1wLy4vanVtcGFwcC9hc3NldHMvanMvc3JjL2luZGV4LmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogRG8gc29tZSBmYW5jeSBVSSBzdHVmZiBpbiBhIHJhdGhlciB1bmZhbmN5IHdheS5cbiAqXG4gKiBAYXV0aG9yIERhbGUgRGF2aWVzIDxkYWxlQGRhbGVkYXZpZXMuY28udWs+XG4gKiBAbGljZW5zZSBNSVRcbiAqL1xuXG5pZiAoSlVNUC5sYXRsb25nICYmIEpVTVAub3dtYXBpa2V5KSB7XG4gICAgdmFyIGxhdGxvbmcgPSBKVU1QLmxhdGxvbmcuc3BsaXQoJywnKTtcbiAgICAvLyBHZXQgc29tZSBkYXRhIGZyb20gdGhlIG9wZW4gd2VhdGhlciBtYXAgYXBpLi4uXG4gICAgZmV0Y2goJ2h0dHBzOi8vYXBpLm9wZW53ZWF0aGVybWFwLm9yZy9kYXRhLzIuNS93ZWF0aGVyP2xhdD0nK2xhdGxvbmdbMF0rJyZsb249JytsYXRsb25nWzFdKycmYXBwaWQ9JytKVU1QLm93bWFwaWtleSlcbiAgICAudGhlbihmdW5jdGlvbihyZXNwKSB7XG4gICAgICAgIC8vIEF0dGVtcHQgdG8gY29udmVydCB0aGUgcmVzcG9uc2UgaW50byBhIGpzb24gb2JqZWN0LlxuICAgICAgICByZXR1cm4gcmVzcC5qc29uKCk7XG4gICAgfSlcbiAgICAudGhlbihmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgIC8vIERldGVybWluZSBpZiB3ZSBzaG91bGQgdXNlIHRoZSBheSBvciBuaWdodCB2YXJpYW50IG9mIG91ciB3ZWF0aGVyIGljb24uXG4gICAgICAgIHZhciBkYXRuaWdodHZhcmlhbnQgPSAnbmlnaHQnO1xuICAgICAgICBpZiAoZGF0YS5kdCA+IGRhdGEuc3lzLnN1bnJpc2UgJiYgZGF0YS5kdCA8IGRhdGEuc3lzLnN1bnNldCkge1xuICAgICAgICAgICAgZGF0bmlnaHR2YXJpYW50ID0gJ2RheSdcbiAgICAgICAgfVxuICAgICAgICAvLyBMaW5rIHRvIHRoZSBjb3JyZWN0IGNpdHkgaW4gb3BlbndlYXRoZXJtYXAgYW5kIGRpc3BsYXkgdGhlIGFwcHJvcHJpYXRlIHdlYXRoZXIgaWNvbi5cbiAgICAgICAgdmFyIGhvbGRlcmVsbSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJy50aW1lLXdlYXRoZXInKTtcbiAgICAgICAgaG9sZGVyZWxtLmhyZWYgKz0gJ2NpdHkvJyArIGRhdGEuc3lzLmlkO1xuICAgICAgICBob2xkZXJlbG0ucXVlcnlTZWxlY3RvcignLndlYXRoZXInKS5jbGFzc0xpc3QuYWRkKCd3aS1vd20tJytkYXRuaWdodHZhcmlhbnQrJy0nK2RhdGEud2VhdGhlclswXS5pZCk7XG4gICAgfSk7XG59XG5cblxuXG5cbiJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==