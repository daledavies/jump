<?php
/**
 * Initialise the application, generate and output page content.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/vendor/autoload.php';

$jumpapp = new Jump\Main();
echo $jumpapp->init();
