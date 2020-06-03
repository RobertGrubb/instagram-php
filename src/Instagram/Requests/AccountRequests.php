<?php

namespace Instagram\Requests;

use Instagram\Core\Exceptions\InstagramException;

use Instagram\Core\Resources\GraphQueries;
use Instagram\Core\Resources\Endpoints;

use Instagram\Core\Normalize\UserSearch;

use Instagram\Core\Models\Media;
use Instagram\Core\Models\Account;

class AccountRequests
{

  /**
   * Request instance holder
   */
  private $graphRequest = null;
  private $domRequest   = null;
  private $jsonRequest  = null;
  private $apiRequest   = null;
  private $endpoints    = null;
  private $GraphQueries = null;

  /**
   * Class constructor
   */
  public function __construct($graphRequest, $domRequest, $jsonRequest, $apiRequest) {
    $this->graphRequest = $graphRequest;
    $this->domRequest = $domRequest;
    $this->jsonRequest = $jsonRequest;
    $this->apiRequest = $apiRequest;
    $this->endpoints = new Endpoints();
    $this->queries = new GraphQueries();
  }

  public function search ($vars = [], $headers = []) {
    $defaultVars = [ 'count' => 10 ];
    $vars = array_merge($vars, $defaultVars);
    if (!isset($vars['query'])) return false;
    $endpoint = $this->endpoints->get('user-search', $vars);
    $response = $this->jsonRequest->call($endpoint, $headers);

    if (!isset($response->users)) return false;
    $items = UserSearch::process($response->users);

    return $items;
  }

  public function byId ($id, $headers = []) {
    $endpoint = $this->endpoints->get('user-id', [ 'id' => $id ]);
    $response = $this->apiRequest->call($endpoint, $headers);
    return $response;
  }

  public function byUsername ($username, $headers = []) {
    $items = $this->search([ 'query' => $username, 'count' => 30 ]);

    $response = false;

    if ($items[0]->username !== strtolower($username)) {
      return false;
    }

    $user = $items[0];

    // Sleep between the requests to be safe.
    sleep(2);

    $response = $this->byId($user->pk, $headers);

    $model = new Account();
    $response = $model->convert($response->user);

    return $response;
  }

  public function get($vars = [], $headers = []) {
    $query = $this->queries->get('user');
    $response = $this->graphRequest->build($query, $vars)->call($headers);

    if (!isset($response->data)) return false;
    if (!isset($response->data->user)) return false;
    if (!isset($response->data->user->reel)) return false;
    if (!isset($response->data->user->reel->user)) return false;

    $userData = $response->data->user->reel->user;

    return $userData;
  }

  public function medias($vars = [], $headers = []) {
    $query = $this->queries->get('feed');
    $response = $this->graphRequest->build($query, $vars)->call($headers);

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
