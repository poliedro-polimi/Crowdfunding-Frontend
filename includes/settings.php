<?php

use nigiri\themes\Theme;

return [
    'theme' => new Theme(),
    'params' => [
        'email' => '',
        'site_name' => 'My Site Name',
        'clean_urls' => true,//Set to true if URL Rewriting is active
        'url_prefix' => '',//A prefix for the URL. Useful if the site is in a subdirectory
        'default_page' => 'site/home',//the home page, the one to show if there is no page requested
        'debug' => true,

        /** Defines views to be used to render each type of Exception.
         * Keys of the array must be Exception names (with full namespace) values must be in the format "ThemeClass:ViewFileName */
        'exceptions_views' => []
    ]
];
