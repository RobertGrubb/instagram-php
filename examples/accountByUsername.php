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

/**
 * The cookie is a required header for this to work, or else
 * it's not going to return all of the data the AccountModel expects.
 */
$data = $scraper->account->byUsername('nfl', [ 'Cookie: ' . $config['session'] ]);

print_r($data);
