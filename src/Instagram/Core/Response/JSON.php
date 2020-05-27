<?php

namespace Instagram\Core\Response;

// Exceptions
use Instagram\Core\Exceptions\InstagramException;

// Validations
use Instagram\Core\Validations\AccountValidation;

// Models
use Instagram\Core\Models\Account;

class JSON {

  private $data;
  private $json;

  public function set ($json) {
    $this->json = json_decode($json);
    return $this;
  }

  public function data ($type) {

    switch ($type) {

      case 'user/api/json':
        $this->data = $this->json;
        break;

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
