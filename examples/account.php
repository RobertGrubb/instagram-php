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
 * Call the below with:
 *
 * Parameter 1: instagram handle
 * Parameter 2: source (page, json)
 *
 * Difference between page and json:
 *
 * page scrapes from the document on the user's profile,
 * json calls the __a=1 route for the json response.
 */
$data = $scraper->account->get('_mattGrubb', 'json');

print_r($data);
