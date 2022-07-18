<?php
/**
 * Proxy requests to Unsplash API and cache response.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/../vendor/autoload.php';

$config = new Jump\Config();
$cache = new Jump\Cache($config);

// If this script is run via CLI then clear the cache and repopulate it,
// otherwise if run via web then get image data from cache and run this
// script asynchronously to refresh the cache for next time.
if (http_response_code() === false) {
    $unsplashdata = Jump\Unsplash::load_cache_unsplash_data($config);
    $cache->save(cachename: 'unsplash', data: $unsplashdata);
    die('Cached data from Unsplash');
}
