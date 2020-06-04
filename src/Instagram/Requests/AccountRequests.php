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
use Instagram\Core\Models\Story;
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

  // setError function from parent.
  private $instance = null;

  /**
   * Class constructor
   */
  public function __construct (
    $instance,
    $graphRequest,
    $domRequest,
    $jsonRequest,
    $apiRequest
  ) {
    $this->instance     = $instance;
    $this->graphRequest = $graphRequest;
    $this->domRequest   = $domRequest;
    $this->jsonRequest  = $jsonRequest;
    $this->apiRequest   = $apiRequest;
    $this->endpoints    = new Endpoints();
    $this->queries      = new GraphQueries();
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

    if (!$response) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No response'
      ]);

      return false;
    }

    if (!isset($response->users)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No users array found'
      ]);

      return false;
    }

    // Normalize the data
    $items = UserSearch::process($response->users);

    return $items;
  }

  /**
   * Sends a request to i.instagram.com to get information
   * for the user id.
   */
  public function byId ($id, $headers = [], $returnRaw = false) {
    $endpoint = $this->endpoints->get('user-id', [ 'id' => $id ]);
    $response = $this->apiRequest->call($endpoint, $headers);

    if (!$response) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No response'
      ]);

      return false;
    }

    if ($returnRaw === false) {

      // Convert to the model structure.
      $model = new Account();
      $response = $model->convert($response->user);
    }

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

    if (count($items) === 0) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No users found for ' . $username
      ]);

      return false;
    }

    $response = false;

    if ($items[0]->username !== strtolower($username)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No users found for ' . $username
      ]);

      return false;
    }

    $user = $items[0];

    // Sleep between the requests to be safe.
    sleep(2);

    $response = $this->byId($user->pk, $headers, true);

    if (!$response) {

      $this->instance->setError([
        'error' => true,
        'message' => 'Could not retrieve data for ' . $username
      ]);

      return false;
    }

    // Convert to the model structure.
    $model = new Account();
    $response = $model->convert($response->user);

    return $response;
  }

  /**
   * Get information for an account by username.
   *
   * This route requires you to now be logged in.
   */
  public function get ($username, $headers = []) {
    $endpoint = $this->endpoints->get('user-page', [ 'username' => $username ]);
    $response = $this->jsonRequest->call($endpoint, $headers);

    if (!$response) {

      $this->instance->setError([
        'error' => true,
        'message' => 'Could not retrieve data for ' . $username
      ]);

      return false;
    }

    if (!isset($response->graphql)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No graphql object found'
      ]);

      return false;
    }

    if (!isset($response->graphql->user)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No user object found'
      ]);

      return false;
    }

    // Convert to the model structure.
    $model = new Account();
    $response = $model->convertFromPage($response->graphql->user);

    return $response;
  }

  /**
   * Gets medias for a specific user id.
   *
   * Call with:
   *
   * [ 'id' => 1234566788 ]
   */
  public function medias ($vars = [], $headers = []) {
    $query = $this->queries->get('feed');
    $response = $this->graphRequest->build($query, $vars)->call($headers);

    if (!isset($response->data)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No data object found'
      ]);

      return false;
    }

    if (!isset($response->data->user)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No user object found'
      ]);

      return false;
    }

    if (!isset($response->data->user->edge_owner_to_timeline_media)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No edge_owner_to_timeline_media object found'
      ]);

      return false;
    }

    if (!isset($response->data->user->edge_owner_to_timeline_media->edges)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No edges array found'
      ]);

      return false;
    }

    $medias = $response->data->user->edge_owner_to_timeline_media->edges;

    $items = [];

    foreach ($medias as $media) {
      $model = new Media();
      $items[] = $model->convert($media->node);
    }

    return $items;
  }

  /**
   * Sends a request to i.instagram.com to get information
   * for the user's stories.
   */
  public function stories ($id, $headers = []) {
    $endpoint = $this->endpoints->get('user-stories', [ 'id' => $id ]);
    $response = $this->apiRequest->call($endpoint, $headers);

    if (!$response) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No response'
      ]);

      return false;
    }

    if (!$response->items) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No items array found'
      ]);

      return false;
    }

    $stories = $response->items;

    $items = [];

    foreach ($stories as $story) {
      $model = new Story();
      $items[] = $model->convert($story);
    }

    return $items;
  }
}
