<?php
// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/vendor/autoload.php';

// Initialise the application, then render and output its index page.
$jumpapp = new Jump\Main();
$jumpapp->build_index_page();
echo $jumpapp->get_output();
