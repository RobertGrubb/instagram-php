<?php

namespace Instagram\Libraries;

use Instagram\Exceptions\InstagramException;

class DOMResponse {

  private $data;
  private $dom;

  public function set ($dom) {
    $this->dom = $dom;
    return $this;
  }

  public function pick ($type) {

    switch ($type) {

      case 'account':
        $this->data = $this->getAccountData();
        break;

      default:
        $this->data = false;
        break;
    }

    return $this->data;
  }

  private function getAccountData () {
    preg_match('/window._sharedData\s\=\s(.*?)\;<\/script>/', $this->dom, $data);
    $userArray = json_decode($data[1], true, 512, JSON_BIGINT_AS_STRING);

    if (!isset($userArray['entry_data'])) {
        throw new InstagramEncodedException('Data response incorrect');
    }

    print_r($userArray['entry_data']);

    if (!isset($userArray['entry_data']['ProfilePage'])) {
        throw new InstagramEncodedException('Data response incorrect');
    }

    if (!isset($userArray['entry_data']['ProfilePage'][0])) {
        throw new InstagramEncodedException('Data response incorrect');
    }

    if (!isset($userArray['entry_data']['ProfilePage'][0]['graphql'])) {
        throw new InstagramEncodedException('Data response incorrect');
    }

    if (!isset($userArray['entry_data']['ProfilePage'][0]['graphql']['user'])) {
        throw new InstagramEncodedException('Data response incorrect');
    }

    return $userArray['entry_data']['ProfilePage'][0]['graphql']['user'];
  }

}
