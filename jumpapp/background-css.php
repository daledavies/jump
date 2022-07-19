<?php
/**
 *      ██ ██    ██ ███    ███ ██████
 *      ██ ██    ██ ████  ████ ██   ██
 *      ██ ██    ██ ██ ████ ██ ██████
 * ██   ██ ██    ██ ██  ██  ██ ██
 *  █████   ██████  ██      ██ ██
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @copyright Copyright (c) 2022, Dale Davies
 * @license MIT
 */

/**
 * Generate dynamic CSS for randomising the background image.
 */

// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/vendor/autoload.php';

$config = new Jump\Config();

$blur = floor((int)$config->get('bgblur', false) / 100 * 15);
$brightness = (int)$config->get('bgbright', false) ? (int)$config->get('bgbright', false) / 100 : 1;

$bgurlstring = '';

// Use unsplash API for background images if provided, otherwise use altbgprovider.
// If none of the above have been provided then fall back to local image.
if ($config->get('unsplashapikey', false) == null) {
    if ($config->get('altbgprovider', false) != null) {
        $backgroundimageurl = $config->get('altbgprovider', false);
    } else {
        $backgroundimageurl = (new Jump\Background($config))->get_random_background_file();
    }
    $bgurlstring = 'background-image: url("'.$backgroundimageurl.'");';
}

header('Content-Type: text/css');
echo '.background {'.$bgurlstring.'filter: brightness('.$brightness.') blur('.$blur.'px);}';
