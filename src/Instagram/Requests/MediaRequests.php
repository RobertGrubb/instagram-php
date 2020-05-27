<?php

namespace Instagram\Requests;

use Instagram\Core\Libraries\Request;
use Instagram\Core\Exceptions\InstagramException;

class MediaRequests
{

  /**
   * Request instance holder
   */
  private $request = null;

  /**
   * Debug logger
   */
  private $log = null;

  /**
   * Class constructor
   */
  public function __construct($request) {
    $this->request = $request;
  }

  /**
   * Get media data
   * @param  string $username
   * @param  string $src 'Page', 'JSON'
   */
  public function get ($code = null, $src = 'page') {
    if (is_null($code)) throw new InstagramException('No code provided');

    try {
      $response = $this->request
        ->build('media/' . $src, [ 'code' => $code ])
        ->call();
    } catch (InstagramException $e) {
      return (object) [ 'error' => $e->getMessage() ];
    }

    return $response;
  }
}
