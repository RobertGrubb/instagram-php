<?php

namespace Instagram\Core\Models;

class AccountMedias
{

  public $owner = null;
  public $medias = null;


  public function createMediaObject($media) {
    $mediaObject = new Media;
    return $mediaObject->set($media);
  }
}
