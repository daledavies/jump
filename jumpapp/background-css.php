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
$backgroundfile = (new Jump\Background($config))->get_random_background_file();

header('Content-Type: text/css');
echo '.background {background-image: url("'.$backgroundfile.'");}';