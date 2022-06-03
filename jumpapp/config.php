<?php
/**
 * Edit the configuration below to suit your requirements.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

return [
    // The site name is displayed in the browser tab.
    'sitename'       => getenv('SITENAME')       ?:   'Jump',
    // Where on the this code is located.
    'wwwroot'        => getenv('WWWROOT')        ?:   '/var/www/html',

    // Stop retrieving items from the cache, useful for testing.
    'cachebypass'    => getenv('CACHEBYPASS')    ?:   false,
    // Where is the cache storage directory, should not be public.
    'cachedir'       => getenv('CACHEDIR')       ?:   '/var/www/cache',

    // Display alternative layout of sites list.
    'altlayout'      => getenv('ALTLAYOUT')      ?:   false,
    // Should the clock be displayed?
    'showclock'      => getenv('SHOWCLOCK')      ?:   true,
    // 12 hour clock format?
    'ampmclock'      => getenv('AMPMCLOCK')      ?:   false,
    // Show a friendly greeting message rather than "#home".
    'showgreeting'   => getenv('SHOWGREETING')   ?:   true,
    // Show the search bar, requires /search/searchengines.json etc.
    'showsearch'     => getenv('SHOWSEARCH')     ?:   true,
    // Include the robots noindex meta tag in site header.
    'noindex'        => getenv('NOINDEX')        ?:   true,

    // Background blur percentage.
    'bgblur'         => getenv('BGBLUR')         ?:   '70',
    // Background brightness percentage.
    'bgbright'       => getenv('BGBRIGHT')       ?:   '85',
    // Unsplash API key, when added will use Unsplash background images.
    'unsplashapikey' => getenv('UNSPLASHAPIKEY') ?:   false,
    // Unsplash collection name to pick random image from.
    'unsplashcollections' => getenv('UNSPLASHCOLLECTIONS') ?: '',

    // Open Weather Map API key.
    'owmapikey'      => getenv('OWMAPIKEY')      ?:   '',
    // Coordinates for weather location. E.g. 51.509865,-0.118092
    'latlong'        => getenv('LATLONG')        ?:   '',
    // Temperature unit: True = metric / False = imperial.
    'metrictemp'     => getenv('METRICTEMP')     ?:   true,
];
