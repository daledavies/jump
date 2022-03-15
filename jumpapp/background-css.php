<?php
/**
 * Generate dynamic CSS for randomising the background image.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/vendor/autoload.php';

$config = new Jump\Config();
$backgroundimgfile = (new Jump\Background($config))->get_random_background_file();

$blur = floor((int)$config->get('bgblur', false) / 100 * 15);
$brightness = (int)$config->get('bgbright', false) ? (int)$config->get('bgbright', false) / 100 : 1;

header('Content-Type: text/css');
echo '.background {background-image: url("'.$backgroundimgfile.'");filter: brightness('.$brightness.') blur('.$blur.'px);}';