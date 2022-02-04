<?php
// Edit the configuration below to suit your requirements.
return [
    'sitename'    => getenv('SITENAME')    ?:  'Jump',
    'wwwroot'     => getenv('WWWROOT')     ?:  '/var/www/html',
    'cachebypass' => getenv('CACHEBYPASS') ?:  false,
    'cachedir'    => getenv('CACHEDIR')    ?:  '/var/www/cache',
    'noindex'     => getenv('NOINDEX')     ?:  true,
];