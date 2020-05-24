<?php

require 'vendor/autoload.php';
require_once __DIR__ . '/src/Instagram.php';

use Instagram\Scraper;

// Instantiate Instagram Scraper library
$scraper = new Scraper();

// You can call account with the source of 'Page', or 'JSON'.
$data = $scraper->account('_mattGrubb');

print_r($data);
