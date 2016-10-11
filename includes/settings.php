<?php

use nigiri\theme\Theme;

return [
    'theme' => new Theme(),
    'params' => [
        'email' => '',
        'site_name' => 'My Site Name',
        'clean_urls' => true,//Set to true if URL Rewriting is active
        'url_prefix' => '',//A prefix for the URL. Useful if the site is in a subdirectory
        'debug' => true
    ]
];
