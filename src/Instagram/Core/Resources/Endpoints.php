<?php

namespace Instagram\Core\Resources;

class Endpoints {

  public function __construct () {
    $this->endpoints = [

      /**
       * User Endpoints
       */

       'user/api/json' => (object) [
         'platform' => 'i.instagram.com',
         'url' => 'https://i.instagram.com/api/v1/users/{id}/info/',
         'type' => 'json'
       ],

      'user/account/page' => (object) [
        'platform' => 'www.instagram.com',
        'url' => 'https://www.instagram.com/{user}',
        'type' => 'dom'
      ],

      'user/medias/page' => (object) [
        'platform' => 'www.instagram.com',
        'url' => 'https://www.instagram.com/{user}',
        'type' => 'dom'
      ],

      'user/account/json' => (object) [
        'platform' => 'www.instagram.com',
        'url' => 'https://www.instagram.com/{user}/?__a=1',
        'type' => 'json'
      ],

      /**
       * Media Routes
       */


      'media/page' => (object) [
        'platform' => 'www.instagram.com',
        'url' => 'https://www.instagram.com/p/{code}',
        'type' => 'dom'
      ]
    ];
  }

  public function get ($endpoint) {
    if (isset($this->endpoints[$endpoint])) return $this->endpoints[$endpoint];
    return false;
  }

}
