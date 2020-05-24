<?php

namespace Instagram\Libraries;

use Instagram\Exceptions\InstagramException;

use Instagram\Validations\AccountValidation;
use Instagram\Validations\MediaValidation;

use Instagram\Models\Account;
use Instagram\Models\Media;

class DOMResponse {

  private $data;
  private $dom;

  public function set ($dom) {
    $this->dom = $dom;
    return $this;
  }

  public function data ($type) {

    switch ($type) {

      case 'user/account/page':
        $sharedData = $this->getSharedData();
        $Validator = new AccountValidation;
        $Model     = new Account;
        $data = $Validator->run($type, $sharedData);
        $this->data = $Model->set($type, $data);
        break;

      case 'media/page':
        $sharedData = $this->getSharedData();
        $Validator = new MediaValidation;
        $Model     = new Media;
        $data = $Validator->run($type, $sharedData);
        $this->data = $Model->set($type, $data);
        break;

      default:
        $this->data = false;
        break;
    }

    return $this->data;
  }

  private function getSharedData () {
    preg_match('/window._sharedData\s\=\s(.*?)\;<\/script>/', $this->dom, $data);
    $sharedData = json_decode($data[1], true, 512, JSON_BIGINT_AS_STRING);
    return $sharedData;
  }
}
