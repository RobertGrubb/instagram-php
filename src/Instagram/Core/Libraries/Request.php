<?php

namespace Instagram\Core\Libraries;

use Instagram\Core\Resources\Endpoints;
use Instagram\Core\Exceptions\InstagramException;

class Request {

  public $headers = [
    'accept' => '*/*',
    'X-IG-App-ID' => '936619743392459',
    'X-Requested-With' => 'XMLHttpRequest'
  ];

  public $query;

  public function __construct() {
    $this->headers['x-csrftoken'] = md5(uniqid());
  }

  public function build($query, $vars) {
    $variables = array_merge($query['variables'], $vars);

    // Remove any nulls
    foreach ($variables as $key => $val) if (is_null($val)) unset($variables[$key]);

    $query['variables'] = json_encode($variables);
    $this->query = $query;
    return $this;
  }

  public function request ($customHeaders = []) {

	  // Initiate CURL
	  $ch = curl_init();

		$endpoint = 'https://www.instagram.com/graphql/query/';

    $endpoint = $endpoint . '?' . http_build_query($this->query);

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
            (
              $this->config->proxy['protocol'] === 'https' ?
              CURLPROXY_HTTPS :
              CURLPROXY_HTTP
            )
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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($this->headers, $customHeaders));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Get the response
    $response = curl_exec($ch);

    // Curl info
    $info = curl_getinfo($ch);

    // Close CURL
    curl_close ($ch);

    // Return the response.
    return json_decode($response);
	}

}
