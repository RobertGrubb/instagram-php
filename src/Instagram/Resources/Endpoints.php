<?php

namespace Instagram\Resources;

class Endpoints {

  public function __construct () {
    $this->endpoints = [

      /**
       * User Endpoints
       */

      'User/AccountData' => (object) [
        'url' => 'https://www.instagram.com/{user}',
        'type' => 'dom',
        'model' => 'Instagram\\Models\\Account'
      ]
    ];
  }

  public function get ($endpoint) {
    if (isset($this->endpoints[$endpoint])) return $this->endpoints[$endpoint];
    return false;
  }

}
