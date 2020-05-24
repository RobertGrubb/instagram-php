<?php

namespace Instagram\Libraries;

use Instagram\Exceptions\InstagramException;

use Instagram\Validations\AccountValidation;

use Instagram\Models\Account;

class JSONResponse {

  private $data;
  private $json;

  public function set ($json) {
    $this->json = json_decode($json);
    return $this;
  }

  public function data ($type) {

    switch ($type) {

      case 'user/account/json':
        $Validator = new AccountValidation;
        $Model     = new Account;
        $data = $Validator->run($type, $this->json);
        $this->data = $Model->set($type, $data);
        break;

      default:
        $this->data = false;
        break;
    }

    return $this->data;
  }

}
