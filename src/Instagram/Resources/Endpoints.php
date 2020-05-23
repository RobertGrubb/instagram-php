<?php

namespace Instagram\Resources;

class Endpoints {

  public function __construct () {
    $this->endpoints = [

      /**
       * User Endpoints
       */

      'User/AccountPage' => (object) [
        'url' => 'https://www.instagram.com/{user}',
        'type' => 'dom',
        'model' => 'Instagram\\Models\\Account'
      ],

      'User/AccountJSON' => (object) [
        'url' => 'https://www.instagram.com/{user}/?__a=1',
        'type' => 'json',
        'model' => 'Instagram\\Models\\Account'
      ]
    ];
  }

  public function get ($endpoint) {
    if (isset($this->endpoints[$endpoint])) return $this->endpoints[$endpoint];
    return false;
  }

}
