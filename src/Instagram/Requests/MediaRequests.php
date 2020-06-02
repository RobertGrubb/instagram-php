<?php

namespace Instagram\Requests;

use Instagram\Core\Libraries\Request;
use Instagram\Core\Exceptions\InstagramException;

use Instagram\Core\Resources\GraphQueries;

use Instagram\Core\Models\Media;

class MediaRequests
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
    $this->queries = new GraphQueries();
  }

  public function get($vars = []) {
    $query = $this->queries->get('media');
    $response = $this->request->build($query, $vars)->request();

    if (!isset($response->data)) return false;
    if (!isset($response->data->shortcode_media)) return false;

    $model = new Media();
    $item = $model->convert($response->data->shortcode_media);

    return $item;
  }
}
