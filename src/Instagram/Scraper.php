<?php

namespace Instagram;

use Instagram\Libraries\Request;
use Instagram\Exceptions\InstagramException;

class Scraper
{

  /**
   * Default configuration
   */
  private $config  = null;

  /**
   * Request instance holder
   */
  private $request = null;

  /**
   * Class constructor
   */
  public function __construct($config = null) {

    /**
     * Set the initial configuration variables.
     */
    $this->_setInitial($config);

    /**
     * Instantiate the request instance.
     */
    $this->request = new Request($this->config);
  }

  public function account ($username = null) {
    if (is_null($username)) throw new InstagramException('No username provided');

    $response = $this->request
      ->build('User/AccountPage', [ 'user' => $username ])
      ->call();

    return $response;
  }


  /**
   * Get a configuration variable from the
   * class configuration.
   */
  private function _get ($var) {
    if (isset($this->config->{$var})) return $var;
    return false;
  }

  /**
   * Sets the default class configuration
   */
  private function _setInitial ($config) {
    if (!is_null($config)) {
      if (!is_array($config)) return false;

      foreach ($config as $key => $val) {
        $this->set($key, $val);
      }
    } else {
      $this->config = (object) [];
    }

    return $this;
  }

  /**
   * Set the class configuration variable.
   */
  public function set ($var, $val) {
    $this->config->{$var} = $val;
  }

}
