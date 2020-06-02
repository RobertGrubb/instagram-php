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
  public function __construct($request) {
    $this->request = $request;
    $this->domRequest = new DomRequest();
    $this->queries = new GraphQueries();
  }

  public function get($username) {
    $url = trim($username);
    $response = $this->domRequest->request($url);

    if (!isset($response['entry_data']['ProfilePage'][0]['graphql']['user'])) {
      throw new InstagramException('Data not valid');
    }

    $model = new Account();
    $account = $model->convert($response['entry_data']['ProfilePage'][0]['graphql']['user']);

    return $account;
  }

  public function medias($vars = []) {
    $query = $this->queries->get('feed');
    $response = $this->request->build($query, $vars)->request();

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
