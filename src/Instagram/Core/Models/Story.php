<?php

namespace Instagram\Core\Models;

use Instagram\Core\Models\Account;

class Story
{

  public $id = null;
  public $code = null;
  public $createdTime;
  public $expiringAt;
  public $type;
  public $imageLowResolutionUrl;
  public $imageThumbnailUrl;
  public $imageStandardResolutionUrl;
  public $imageHighResolutionUrl;
  public $caption;
  public $captionIsEdited;
  public $videoLowResolutionUrl;
  public $videoStandardResolutionUrl;
  public $videoLowBandwidthUrl;
  public $owner;
  public $ownerId;

  /**
   * Convert raw data to the model structure
   */
  public function convert ($media) {
    $instance = new self();

    $instance->id = $media->id;
    $instance->type = ($media->media_type == 2 ? 'video' : 'image');

    if (isset($media->caption_is_edited)) {
      $instance->captionIsEdited = $media->caption_is_edited;
    }

    $instance->createdTime = $media->taken_at;
    $instance->expiringAt = $media->expiring_at;
    $instance->code = $media->code;

    // Images
    $images = $this->imagesFromVersions($media->image_versions2);
    $instance->imageStandardResolutionUrl = $images['imageStandardResolutionUrl'];
    $instance->imageLowResolutionUrl = $images['imageLowResolutionUrl'];
    $instance->imageHighResolutionUrl = $images['imageHighResolutionUrl'];
    $instance->imageThumbnailUrl = $images['imageThumbnailUrl'];

    // Videos
    $videos = $this->videosFromVersions(isset($media->video_versions) ? $media->video_versions : false);
    $instance->videoLowResolutionUrl = $videos['videoLowResolutionUrl'];
    $instance->videoStandardResolutionUrl = $videos['videoStandardResolutionUrl'];
    $instance->videoLowBandwidthUrl = $videos['videoLowBandwidthUrl'];

    $instance->caption = $media->caption;
    $instance->owner = (new Account())->convert($media->user);
    $instance->ownerId = $media->user->pk;

    return $instance;
  }

  /**
   * Return array of video resolutions.
   */
  private function videosFromVersions ($videoVersions) {
    if (!$videoVersions) {
      return [
        'videoLowResolutionUrl' => false,
        'videoStandardResolutionUrl' => false,
        'videoLowBandwidthUrl' => false
      ];
    }

    $lowRes = $videoVersions[1]->url;
    $lowBandwidth = $videoVersions[1]->url;
    $standardRes = $videoVersions[0]->url;

    return [
      'videoLowResolutionUrl' => $lowRes,
      'videoStandardResolutionUrl' => $standardRes,
      'videoLowBandwidthUrl' => $lowBandwidth
    ];
  }

  /**
   * Return array of image resolutions
   */
  private function imagesFromVersions ($imageVersions) {
    $lowRes  = $imageVersions->candidates[1]->url;
    $highRes = $imageVersions->candidates[0]->url;

    return [
      'imageThumbnailUrl' => $lowRes,
      'imageLowResolutionUrl' => $lowRes,
      'imageStandardResolutionUrl' => $highRes,
      'imageHighResolutionUrl' => $highRes
    ];
  }
}
