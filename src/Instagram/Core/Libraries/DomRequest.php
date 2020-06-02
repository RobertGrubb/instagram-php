<?php

namespace Instagram\Core\Libraries;

use Instagram\Core\Resources\Endpoints;
use Instagram\Core\Exceptions\InstagramException;

class DomRequest {

  public $headers = [
    'accept' => '*/*',
    'X-IG-App-ID' => '936619743392459',
    'X-Requested-With' => 'XMLHttpRequest'
  ];

  public $domHeaders = [
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
  ];

  public function request ($url) {

	  // Initiate CURL
	  $ch = curl_init();

		$endpoint = 'https://www.instagram.com/';

    $endpoint = $endpoint . $url;

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->domHeaders);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Get the response
    $response = curl_exec($ch);

    // Curl info
    $info = curl_getinfo($ch);

    // Close CURL
    curl_close ($ch);

    if ($info['http_code'] !== 200) {
      throw new InstagramException("Code " . $info['http_code'] . " returned");
    }

    $sharedData = $this->getSharedData($response);

    return $sharedData;
	}

  private function getSharedData ($html) {
    preg_match('/window._sharedData\s\=\s(.*?)\;<\/script>/', $html, $data);

    if (!isset($data[1])) return false;

    $sharedData = json_decode($data[1], true, 512, JSON_BIGINT_AS_STRING);
    return $sharedData;
  }

}
