<?php

namespace Instagram\Requests;

// Exceptions
use Instagram\Core\Exceptions\InstagramException;

// Resources
use Instagram\Core\Resources\GraphQueries;
use Instagram\Core\Resources\Endpoints;

// Normalization classes
use Instagram\Core\Normalize\UserSearch;

// Models
use Instagram\Core\Models\Media;
use Instagram\Core\Models\Account;

class AccountRequests
{

  /**
   * Request instance holders
   */
  private $graphRequest = null;
  private $domRequest   = null;
  private $jsonRequest  = null;
  private $apiRequest   = null;

  // All endpoints for dom and api requests
  private $endpoints    = null;

  // GraphQuery data
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

  /**
   * Uses the web route to search for users.
   */
  public function search ($vars = [], $headers = []) {
    $defaultVars = [ 'count' => 10 ];
    $vars = array_merge($vars, $defaultVars);
    if (!isset($vars['query'])) return false;
    $endpoint = $this->endpoints->get('user-search', $vars);
    $response = $this->jsonRequest->call($endpoint, $headers);

    if (!isset($response->users)) return false;

    // Normalize the data
    $items = UserSearch::process($response->users);

    return $items;
  }

  /**
   * Sends a request to i.instagram.com to get information
   * for the user id.
   */
  public function byId ($id, $headers = []) {
    $endpoint = $this->endpoints->get('user-id', [ 'id' => $id ]);
    $response = $this->apiRequest->call($endpoint, $headers);
    return $response;
  }

  /**
   * At the moment only tests the top result for a match
   * in usernames. If it doesn't match, it returns false.
   *
   * Also added a sleep so we don't anger the beast.
   */
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

    // Convert to the model structure.
    $model = new Account();
    $response = $model->convert($response->user);

    return $response;
  }

  /**
   * Get minimal user information via this route.
   *
   * Call it with either:
   *
   * [ 'username' => 'user_name_here' ]
   *
   * or
   *
   * [ 'user_id' => 1234566778 ]
   */
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

  /**
   * Gets medias for a specific user id.
   *
   * Call with:
   *
   * [ 'id' => 1234566788 ]
   */
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
