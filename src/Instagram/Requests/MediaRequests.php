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

  /**
   * Class constructor
   */
  public function __construct($graphRequest, $domRequest, $jsonRequest, $apiRequest) {
    $this->graphRequest = $graphRequest;
    $this->domRequest = $domRequest;
    $this->jsonRequest = $jsonRequest;
    $this->apiRequest = $apiRequest;
    $this->queries = new GraphQueries();
  }

  /**
   * Gets a media with a specific shortcode:
   *
   * [ 'shortcode' => 'qwe23t2ewga' ]
   */
  public function get($vars = [], $headers = []) {
    $query = $this->queries->get('media');
    $response = $this->graphRequest->build($query, $vars)->call($headers);

    if (!isset($response->data)) return false;
    if (!isset($response->data->shortcode_media)) return false;

    $model = new Media();
    $item = $model->convert($response->data->shortcode_media);

    return $item;
  }
}
