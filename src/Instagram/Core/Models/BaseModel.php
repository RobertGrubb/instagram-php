<?php

namespace Instagram\Core\Models;

class BaseModel
{

  public function getValue ($data, $key) {
    if (is_array($data)) {
      if (isset($data[$key])) return $data[$key];
      return null;
    }

    if (is_object($data)) {
      if (isset($data->{$key})) return $data->{$key};
      return null;
    }
  }
}
