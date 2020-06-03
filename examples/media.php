<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/Instagram.php';

use Instagram\Scraper;

$config = require_once __DIR__ . '/env.php';


// Instantiate Instagram Scraper library
$scraper = new Scraper($config);

/**
 * Use the following syntax to get information for a media post.
 */
$data = $scraper->media->get([ 'shortcode' => 'CAdO-8MjgHM' ]);

print_r($data);
