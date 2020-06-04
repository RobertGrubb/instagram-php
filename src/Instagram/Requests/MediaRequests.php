<?php

namespace Instagram\Requests;

// Exceptions
use Instagram\Core\Exceptions\InstagramException;

// Resources
use Instagram\Core\Resources\GraphQueries;

// Models
use Instagram\Core\Models\Media;

class MediaRequests
{

  /**
   * Request instance holders
   */
  private $graphRequest = null;
  private $domRequest = null;
  private $jsonRequest = null;
  private $apiRequest = null;

  // GraphQuery data
  private $GraphQueries = null;

  // setError from parent
  private $instance = null;

  /**
   * Class constructor
   */
  public function __construct(
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
    $this->queries      = new GraphQueries();
  }

  /**
   * Gets a media with a specific shortcode:
   *
   * [ 'shortcode' => 'qwe23t2ewga' ]
   */
  public function get ($vars = [], $headers = []) {
    $query = $this->queries->get('media');
    $response = $this->graphRequest->build($query, $vars)->call($headers);

    if (!$response) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No response'
      ]);

      return false;
    }

    if (!isset($response->data)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No data object found'
      ]);

      return false;
    }

    if (!isset($response->data->shortcode_media)) {
      $this->instance->setError([
        'error' => true,
        'message' => 'No shortcode_media object found'
      ]);

      return false;
    }

    $model = new Media();
    $item = $model->convert($response->data->shortcode_media);

    return $item;
  }
}
