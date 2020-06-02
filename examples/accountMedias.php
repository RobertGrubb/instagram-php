<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/Instagram.php';

/**
 * Get development config
 */
$config = require_once __DIR__ . '/env.php';

use Instagram\Scraper;

// Instantiate Instagram Scraper library
$scraper = new Scraper($config);

// Gets user's most recent medias from the profile page.
$data = $scraper->account->medias([ 'id' => 3926381369 ]);

print_r($data);
