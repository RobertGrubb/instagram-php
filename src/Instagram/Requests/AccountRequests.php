<?php

namespace Instagram\Requests;

use Instagram\Core\Libraries\Request;
use Instagram\Core\Exceptions\InstagramException;

class AccountRequests
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
   * Get account data by id
   */
  public function getById ($id) {
    if (is_null($id)) throw new InstagramException('No user id provided');

    try {
      $response = $this->request
        ->build('user/api/json', [ 'id' => $id ])
        ->call();
    } catch (InstagramException $e) {
      return (object) [ 'error' => $e->getMessage() ];
    }

    return $response;
  }

  /**
   * Gets recent medias from the account's profile.
   */
  public function mediaWithTag ($username = null, $hashtag = null) {
    if (is_null($username)) throw new InstagramException('No username provided');
    if (is_null($hashtag)) throw new InstagramException('No hashtag provided');

    try {
      $response = $this->request
        ->build('user/medias/page', [ 'user' => $username ])
        ->call();

      // Get the medias
      $medias = $response->medias;

      // Reset the response medias array
      $response->medias = [];

      // Itreate through each, check for the tag.
      foreach ($medias as $media) {

        // If the caption contains the tag, set the variable
        if (strpos(strtolower($media->caption), trim(strtolower($hashtag))) !== false) {
          $response->medias[] = $media;
        }
      }
    } catch (InstagramException $e) {
      return (object) [ 'error' => $e->getMessage() ];
    }

    return $response;
  }

  /**
   * Gets recent medias from the account's profile.
   */
  public function recentMedia ($username = null) {
    if (is_null($username)) throw new InstagramException('No username provided');

    try {
      $response = $this->request
        ->build('user/medias/page', [ 'user' => $username ])
        ->call();
    } catch (InstagramException $e) {
      return (object) [ 'error' => $e->getMessage() ];
    }

    return $response;
  }

  /**
   * Get account data
   * @param  string $username
   * @param  string $src 'Page', 'JSON'
   */
  public function get ($username = null, $src = 'page') {
    if (is_null($username)) throw new InstagramException('No username provided');

    try {
      $response = $this->request
        ->build('user/account/' . $src, [ 'user' => $username ])
        ->call();
    } catch (InstagramException $e) {
      return (object) [ 'error' => $e->getMessage() ];
    }

    return $response;
  }
}
