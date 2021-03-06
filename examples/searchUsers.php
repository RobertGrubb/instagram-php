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

try {
  // Searches for users
  $data = $scraper->account->search([ 'query' => '_mattGrubb' ]);

  // Scraper will set an error, and you can check it like so:
  if (!$data && $scraper->error !== false) print_r($scraper->error);

  print_r($data);
} catch (Exception $e) {
  echo $e->getMessage() . PHP_EOL;
}
