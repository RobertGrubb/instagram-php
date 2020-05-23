<?php

require 'vendor/autoload.php';
require_once __DIR__ . '/src/Instagram.php';

use Instagram\Scraper;

// Instantiate Instagram Scraper library
$scraper = new Scraper();

$data = $scraper->account('_mattGrubb');

var_dump($data);
