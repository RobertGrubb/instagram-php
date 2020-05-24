<?php

namespace Instagram\Models;

class Media
{
  public $id = null;
  public $shortcode = null;
  public $code = null;

  /**
   * Set based on endpoint
   */
  public function set ($endpoint, $media) {
    $instance = new self();


    switch ($endpoint) {

      case 'media/page':
        $instance->id = $media['id'];
        $instance->shortcode = $media['shortcode'];
        $instance->code = $media['shortcode'];
        break;
    }

    return $instance;
  }
}
