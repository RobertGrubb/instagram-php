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

// Needs a cookie session to work.
$data = $scraper->account->get([ 'username' => '_mattGrubb' ], [ 'Cookie: ' . $config['session'] ]);

print_r($data);
