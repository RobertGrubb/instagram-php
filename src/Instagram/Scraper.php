<?php

namespace Instagram;

use Instagram\Core\Libraries\Request;
use Instagram\Core\Exceptions\InstagramException;

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

    /**
     * Initialize the requests
     */
    $this->_initialize();
  }

  /**
   * Initializes the different requests
   * classes that need to be loaded for the
   * scraper.
   *
   * Account example: $scraper->account->get('test');
   * Media example: $scraper->media->get('code');
   */
  private function _initialize() {

    $this->account = new \Instagram\Requests\AccountRequests(
      $this->request
    );

    $this->media   = new \Instagram\Requests\MediaRequests(
      $this->request
    );
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
    $this->config = (object) [];

    if (!is_null($config)) {
      if (!is_array($config)) return false;

      foreach ($config as $key => $val) {
        $this->set($key, $val);
      }
    }

    return $this;
  }

  /**
   * Set the class configuration variable.
   */
  public function set ($var, $val) {
    $this->config->{$var} = $val;

    // Update the request class
    if (!is_null($this->request)) $this->request->set($var, $val);
  }

}
