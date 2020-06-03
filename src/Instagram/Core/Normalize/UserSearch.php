<?php

namespace Instagram\Core\Normalize;

class UserSearch
{

  public static function process ($rawData) {
    $response = [];

    foreach ($rawData as $item) {
      $response[] = $item->user;
    }

    return $response;
  }
}
