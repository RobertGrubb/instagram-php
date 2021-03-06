<?php

namespace Instagram\Core\Resources;

class Endpoints {

  public $endpoints = [
    'user-dom'     => 'https://www.instagram.com/{username}/',

    // JsonRequest
    'user-page'    => 'https://www.instagram.com/{username}/?__a=1',
    'user-search'  => 'https://www.instagram.com/web/search/topsearch/?query={query}&count={count}',

    // ApiRequest
    'user-id'      => 'https://i.instagram.com/api/v1/users/{id}/info/',
    'user-stories' => 'https://i.instagram.com/api/v1/feed/user/{id}/reel_media/'
  ];

  public function get($endpoint, $vars = []) {
    if (!isset($this->endpoints[$endpoint])) return false;

    $url = $this->endpoints[$endpoint];

    foreach ($vars as $key => $val) {
      $url = str_replace('{' . $key . '}', $val, $url);
    }

    return $url;
  }
}
