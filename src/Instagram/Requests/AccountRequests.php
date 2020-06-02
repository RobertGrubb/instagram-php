<?php

namespace Instagram\Requests;

use Instagram\Core\Libraries\Request;
use Instagram\Core\Libraries\DomRequest;

use Instagram\Core\Exceptions\InstagramException;

use Instagram\Core\Resources\GraphQueries;

use Instagram\Core\Models\Media;
use Instagram\Core\Models\Account;

class AccountRequests
{

  /**
   * Request instance holder
   */
  private $request = null;
  private $GraphQueries = null;

  /**
   * Class constructor
   */
  public function __construct($request, $domRequest) {
    $this->request = $request;
    $this->domRequest = $domRequest;
    $this->queries = new GraphQueries();
  }

  public function get($vars = [], $headers = []) {
    $query = $this->queries->get('user');
    $response = $this->request->build($query, $vars)->request($headers);

    if (!isset($response->data)) return false;
    if (!isset($response->data->user)) return false;
    if (!isset($response->data->user->reel)) return false;
    if (!isset($response->data->user->reel->user)) return false;

    $userData = $response->data->user->reel->user;

    return $userData;
  }

  public function medias($vars = [], $headers = []) {
    $query = $this->queries->get('feed');
    $response = $this->request->build($query, $vars)->request($headers);

    if (!isset($response->data)) return false;
    if (!isset($response->data->user)) return false;
    if (!isset($response->data->user->edge_owner_to_timeline_media)) return false;
    if (!isset($response->data->user->edge_owner_to_timeline_media->edges)) return false;

    $medias = $response->data->user->edge_owner_to_timeline_media->edges;

    $items = [];

    foreach ($medias as $media) {
      $model = new Media();
      $items[] = $model->convert($media->node);
    }

    return $items;
  }
}
