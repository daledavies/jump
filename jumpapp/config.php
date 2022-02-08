<?php
/**
 * Edit the configuration below to suit your requirements.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

return [
    // The site name is displayed in the browser tab.
    'sitename'    => getenv('SITENAME')    ?:  'Jump',
    // Where on the this code is located.
    'wwwroot'     => getenv('WWWROOT')     ?:  '/var/www/html',
    // Stop retrieving items from the cache, useful for testing.
    'cachebypass' => getenv('CACHEBYPASS') ?:  false,
    // Where is the cache storage directory, should not be public.
    'cachedir'    => getenv('CACHEDIR')    ?:  '/var/www/cache',
    // Include the robots noindex meta tag in site header.
    'noindex'     => getenv('NOINDEX')     ?:  true,
    // Coordinates for weather location. E.g. 51.509865,-0.118092
    'latlong'     => getenv('LATLONG')     ?:  '',
    // Open Weather Map API key.
    'owmapikey'   => getenv('OWMAPIKEY')   ?:  '',
];