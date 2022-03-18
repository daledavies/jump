<?php
/**
 * Return icon image data for a given site from sites.json
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/../vendor/autoload.php';

$config = new Jump\Config();
$cache = new Jump\Cache($config);
$sites = new Jump\Sites($config, $cache);

$siteurl = isset($_GET['siteurl']) ? filter_var($_GET['siteurl'], FILTER_SANITIZE_URL) : (throw new Exception('siteurl param not provided'));

$site = $sites->get_site_by_url($siteurl);

$imagedata =  $site->get_favicon_image_data();

// We made it here so output the API response as json.
header('Content-Type: '.$imagedata->mimetype);
echo $imagedata->data;
