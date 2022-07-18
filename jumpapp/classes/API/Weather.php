<?php

namespace Jump\API;

class Weather extends AbstractAPI {

    public function get_output(): string {

        $this->validate_token();

        // Start of variables  we want to use.
        $owmapiurlbase = 'https://api.openweathermap.org/data/2.5/weather';
        $units = $this->config->parse_bool($this->config->get('metrictemp')) ? 'metric' : 'imperial';

        // If we have either lat or lon query params then cast them to a float, if not then
        // set the values to zero.
        $lat = isset($this->routeparams['lat']) ? (float) $this->routeparams['lat'] : 0;
        $lon = isset($this->routeparams['lon']) ? (float) $this->routeparams['lon'] : 0;

        // Use the lat and lon values provided unless they are zero, this might mean that
        // either they werent provided as query params or they couldn't be cast to a float.
        // If they are zero then use the default latlong from config.
        $latlong = [$lat, $lon];
        if ($lat === 0 || $lon === 0) {
            $latlong = explode(',', $this->config->get('latlong', false));
        }

        // This is the API endpoint and params we are using for the query,
        $url =  $owmapiurlbase
                .'?units=' . $units
                .'&lat=' . $latlong[0]
                .'&lon=' . $latlong[1]
                .'&appid=' . $this->config->get('owmapikey', false);

        // Use the cache to store/retrieve data, make an md5 hash of latlong so it is not possible
        // to track location history form the stored cache.
        $weatherdata = $this->cache->load(cachename: 'weatherdata', key: md5(json_encode($latlong)), callback: function() use ($url) {
            // Ask the API for some data.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            $response = curl_exec($ch);

            // Just in case something went wrong with the request we'll capture the error.
            if (curl_errno($ch)) {
                $curlerror = curl_error($ch);
            }
            curl_close($ch);
            // If we had an error then return the error message and exit, otherwise return the API response.
            if (isset($curlerror)) {
                http_response_code(400);
                die(json_encode(['error' => $curlerror]));
            }
            return $response;
        });

        // We made it here so return the API response as a json string.
        return $weatherdata;
    }

}
