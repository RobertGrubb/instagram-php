<?php

namespace Instagram\Requests;

use Instagram\Core\Exceptions\InstagramException;

use Instagram\Core\Resources\GraphQueries;

use Instagram\Core\Models\Media;

class MediaRequests
{

  /**
   * Request instance holder
   */
  private $graphRequest = null;
  private $domRequest = null;
  private $jsonRequest = null;
  private $apiRequest = null;
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
