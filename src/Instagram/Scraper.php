<?php

namespace Instagram;

class Scraper
{

  /**
   * Default configuration
   */
  private $config = null;

  /**
   * Class constructor
   */
  public function __construct($config = null) {
    return $this->_setInitial($config);
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

      return $this;
    }
  }

  /**
   * Set the class configuration variable.
   */
  public function set ($var, $val) {
    $this->config->{$var} = $val;
  }

}
