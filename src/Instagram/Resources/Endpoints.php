<?php

namespace Instagram\Resources;

class Endpoints {

  public function __construct () {
    $this->endpoints = [

      /**
       * User Endpoints
       */

      'user/account/page' => (object) [
        'url' => 'https://www.instagram.com/{user}',
        'type' => 'dom',
        'model' => 'Instagram\\Models\\Account'
      ],

      'user/account/json' => (object) [
        'url' => 'https://www.instagram.com/{user}/?__a=1',
        'type' => 'json',
        'model' => 'Instagram\\Models\\Account'
      ],

      /**
       * Media Routes
       */


      'media/page' => (object) [
        'url' => 'https://www.instagram.com/p/{code}',
        'type' => 'dom',
        'model' => 'Instagram\\Models\\Media'
      ]
    ];
  }

  public function get ($endpoint) {
    if (isset($this->endpoints[$endpoint])) return $this->endpoints[$endpoint];
    return false;
  }

}
