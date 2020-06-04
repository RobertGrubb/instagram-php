<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/../src/Instagram.php';

use Instagram\Scraper;

$config = require_once __DIR__ . '/env.php';


// Instantiate Instagram Scraper library
$scraper = new Scraper($config);

try {
  /**
   * Use the following syntax to get information for a media post.
   */
  $data = $scraper->media->get([ 'shortcode' => 'CAdO-8MjgHM' ]);

  // Scraper will set an error, and you can check it like so:
  if (!$data && $scraper->error !== false) print_r($scraper->error);

  print_r($data);
} catch (Exception $e) {
  echo $e->getMessage() . PHP_EOL;
}
