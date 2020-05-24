<?php

namespace Instagram\Validations;

use Instagram\Exceptions\InstagramException;

/**
 * Validation takes in the raw data, verifies
 * that the payload includes what it should,
 * then returns the specific part of the data
 * it's supposed to return.
 *
 * Based on the endpoint key.
 */
class MediaValidation
{

  public function run ($endpoint, $media) {

    $data = false;

    switch ($endpoint) {

      case 'media/page':

        if (!isset($media['entry_data'])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($media['entry_data']['PostPage'])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($media['entry_data']['PostPage'][0])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($media['entry_data']['PostPage'][0]['graphql'])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($media['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
            throw new InstagramException('Data response incorrect');
        }

        $data = $media['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
        break;
    }

    return $data;
  }
}
