<?php

namespace Instagram\Libraries;

use Instagram\Resources\Endpoints;
use Instagram\Libraries\DOMResponse;
use Instagram\Exceptions\InstagramException;

class Request {

  /**
   * @class Instagram\Resources\Endpoints instance
   */
  private $endpoints = null;

  /**
   * @class Instagram\Libraries\DOMResponse
   */
  private $dom = null;

  /**
   * Scraper configuration
   */
  private $config    = null;

  /**
   * Specific call data
   */
  private $endpoint  = '';
  private $endpointData = [];
  private $endpointDataKey = false;

  public function __construct ($config) {
    $this->endpoints = new Endpoints();
    $this->config = $config;
    $this->dom = new DOMResponse();
    return $this;
  }

  public function build (
    $endpoint,
    $params = []
  ) {

    /**
     * If endpoint does not exist, throw a new exception.
     */
    if ($this->endpoints->get($endpoint) === false) {
      throw new InstagramException('Endpoint does not exist');
    }

    $this->endpointDataKey = $endpoint;

    // Set the endpointData
    $this->endpointData = $this->endpoints->get($endpoint);

    // Set the endpoint
    $this->endpoint = $this->endpointData->url;

    // If no params, end it here.
    if (!is_array($params)) return $this;

    // Iterate through the params, and replace them.
    foreach ($params as $key => $val) {
      $replace = "{{$key}}";

      $this->endpoint = str_replace(
        $replace,
        $this->check($key, $val),
        $this->endpoint
      );
    }

    // Return this instance.
    return $this;
  }

  public function call () {
	  // Initiate CURL
	  $ch = curl_init();

		$endpoint = $this->endpoint;

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $endpoint);

		// Proxy setup:
		if (isset($this->config->proxy)) {

			// Address should be 0.0.0.0:0000
			curl_setopt($ch, CURLOPT_PROXY, $this->config->proxy['address']);

			// Auth should be: username:password
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config->proxy['auth']);
		}

    // Set other headers
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());

    // Get the response
    $response = curl_exec($ch);

    // Close CURL
    curl_close ($ch);

    // Check for empty response
		if (empty($response)) return [ 'error' => 'No response' ];

    if ($this->endpointData->type === 'dom') {
      $response = $this->dom->set($response)->pick($this->endpointDataKey);
    } else {

      // Decode the response
      $response = json_decode($response);
    }

    if (isset($this->endpointData->model)) {
      $model = new $this->endpointData->model;
      $response = $model->set($this->endpointDataKey, $response);
    }

    // Return the response.
    return $response;
	}

  /**
   * Class helpers
   */

  private function headers () {
    $headers = [];

    $headers['user-agent'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';
    $headers['x-requested-with'] = 'XMLHttpRequest';
    $headers['accept'] = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9";
    $headers['x-csrftoken'] = md5(uniqid());
    //$headers['urlgen'] = "{\"72.90.74.51\": 701}:1jcc5b:tVIlRrk2_W2ED5O5wYNYh35CvZ4";

    return $headers;
  }

  private function check($key, $val) {
    $keysToClean = ['user'];
    if (!in_array($key, $keysToClean)) return $val;
    return $this->clean($val);
  }

  private function clean ($value) {
    return trim($value);
  }
}
