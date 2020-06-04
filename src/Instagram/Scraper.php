<?php

namespace Instagram;

use Instagram\Core\Libraries\GraphRequest;
use Instagram\Core\Libraries\DomRequest;
use Instagram\Core\Libraries\JsonRequest;
use Instagram\Core\Libraries\ApiRequest;

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
  private $graphRequest = null;

  /**
   * DomRequest instance holder
   */
  private $domRequest = null;

  /**
   * Error information
   */
  public $error = false;

  /**
   * Class constructor
   */
  public function __construct($config = null) {

    /**
     * Set the initial configuration variables.
     */
    $this->_setInitial($config);

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

    /**
     * Instantiate the request instance.
     */
    $this->graphRequest = new GraphRequest($this->config);
    $this->domRequest   = new DomRequest($this->config);
    $this->jsonRequest  = new JsonRequest($this->config);
    $this->apiRequest   = new ApiRequest($this->config);

    $this->account = new \Instagram\Requests\AccountRequests(
      $this,
      $this->graphRequest,
      $this->domRequest,
      $this->jsonRequest,
      $this->apiRequest
    );

    $this->media   = new \Instagram\Requests\MediaRequests(
      $this,
      $this->graphRequest,
      $this->domRequest,
      $this->jsonRequest,
      $this->apiRequest
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

    // Re initialize
    $this->_initialize();
  }

  public function setError ($error) {
    $this->error = $error;
  }

}
