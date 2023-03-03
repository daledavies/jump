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
$customwidth = (int)$config->get('customwidth', false);

header('Content-Type: text/css');
if ($customwidth > 0) {
    echo '.content {max-width: '.$customwidth.'px;}';
}
