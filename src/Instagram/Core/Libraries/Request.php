<?php

namespace Instagram\Core\Libraries;

use Instagram\Core\Resources\Endpoints;
use Instagram\Core\Response\DOM;
use Instagram\Core\Response\JSON;
use Instagram\Core\Exceptions\InstagramException;

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
   * @class Instagram\Libraries\JSONResponse
   */
  private $json = null;

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

  /**
   * Instagram request headers
   */

  private $platformHeaders = [

    /**
     * Attempt to mimic the actual request as close as possible.
     */
    'www.instagram.com' => [
      'authority'       => 'www.instagram.com',
      'cache-control'   => 'max-age=0',
      'upgrade-insecure-requests' => '1',
      'accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
      'sec-fetch-site'  => 'same-orgin',
      'sec-fetch-mode'  => 'navigate',
      'sec-fetch-user'  => '?1',
      'sec-fetch-dest'  => 'document',
      'accept-language' => 'en-US,en;q=0.9',
      'user-agent'      => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Instagram 105.0.0.11.118 (iPhone11,8; iOS 12_3_1; en_US; en-US; scale=2.00; 828x1792; 165586599)'
    ],

    /**
     * For any endpoints that hit the current platform,
     * use these headers.
     */
    'i.instagram.com' => [
      'host'              => 'i.instagram.com',
      'x-ig-capabilities' => '3w==',
      'user-agent'        => 'Instagram 9.5.1 (iPhone9,2; iOS 10_0_2; en_US; en-US; scale=2.61; 1080x1920) AppleWebKit/420+',
    ]
  ];

  public function __construct ($config) {
    $this->endpoints = new Endpoints();
    $this->config = $config;
    $this->dom = new DOM();
    $this->json = new JSON();
    return $this;
  }

  /**
   * Builds the endpoint that needs to be requested.
   */
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
    $timeout = isset($this->config->timeout) ? $this->config->timeout : 30;

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

		// Proxy setup:
		if (isset($this->config->proxy)) {

      // Make sure address is set.
      if (isset($this->config->proxy['address'])) {

        // Check for protocol setting
        if (isset($this->config->proxy['protocol'])) {

          // Auth should be: username:password
    			curl_setopt(
            $ch,
            CURLOPT_PROXYTYPE,
            ($this->config->proxy['protocol'] === 'https' ? CURLPROXY_HTTPS : CURLPROXY_HTTP)
          );
        } else {
          curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }

        // Address should be 0.0.0.0:0000
  			curl_setopt($ch, CURLOPT_PROXY, $this->config->proxy['address']);

        // Check if auth is provided
        if (isset($this->config->proxy['auth'])) {

          // Auth should be: username:password
          curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config->proxy['auth']);
        }
      }
		}

    // Set other headers
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());

    // Get the response
    $response = curl_exec($ch);

    // Curl info
    $info = curl_getinfo($ch);

    // Close CURL
    curl_close ($ch);

    if ($info['http_code'] !== 200) {

      if (isset($this->config->debug)) {
        if ($this->config->debug === true) {
          echo $endpoint . PHP_EOL;
          print_r($info);
          print_r(json_decode($response));
        }
      }

      throw new InstagramException("Code " . $info['http_code'] . " returned");
    }

    // Check for empty response
		if (empty($response)) return [ 'error' => 'No response' ];

    if ($this->endpointData->type === 'json' && strpos($response, 'error') !== false) {
      throw new InstagramException(json_decode($response));
    }

    $response = $this->{$this->endpointData->type}
      ->set($response)
      ->data($this->endpointDataKey);

    // Return the response.
    return $response;
	}

  /**
   * =============================
   * Class helpers
   * =============================
   */

  private function headers () {
    $headers = $this->platformHeaders[$this->endpointData->platform];
    $customHeaders = (isset($this->config->headers) ? $this->config->headers : []);
    $headers = array_merge($headers, $customHeaders);

    if (!isset($headers['x-csrftoken'])) $headers['x-csrftoken'] = md5(uniqid());

    if (isset($this->config->session)) {
      if (is_array($this->config->session)) {
        $headers['cookie'] = '';

        foreach ($this->config->session as $key => $val) {
          $headers['cookie'] .= "$key=$val; ";
        }
      }

      if (is_string($this->config->session)) {
        $headers['cookie'] = $this->config->session;
      }
    }

    return $headers;
  }

  /**
   * Check if a variable needs to be cleaned.
   */
  private function check($key, $val) {
    $keysToClean = ['user', 'tag'];
    if (!in_array($key, $keysToClean)) return $val;
    return $this->clean($val);
  }

  /**
   * Cleanse a variable of spaces
   */
  private function clean ($value) {
    return trim($value);
  }

  /**
   * Set a configuration variable from outside of the class.
   */
  public function set($var, $val) {
    $this->config->{$var} = $val;
  }
}
