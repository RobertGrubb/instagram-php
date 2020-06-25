<?php

namespace Instagram\Core\Models;

class Media
{
  public $id = null;
  public $shortcode = null;
  public $code = null;

  public $createdTime;
  public $type;
  public $link;
  public $imageLowResolutionUrl;
  public $imageThumbnailUrl;
  public $imageStandardResolutionUrl;
  public $imageHighResolutionUrl;
  public $carouselMedia;
  public $caption;
  public $captionIsEdited;
  public $isAd;
  public $videoLowResolutionUrl;
  public $videoStandardResolutionUrl;
  public $videoLowBandwidthUrl;
  public $videoViews;
  public $owner;
  public $ownerId;
  public $likesCount;
  public $locationId;
  public $locationName;
  public $commentsCount;

  /**
   * Convert raw data to the model structure
   */
  public function convert ($media) {
    $instance = new self();

    $instance->id = $media->id;
    $instance->type = 'image';
    if ($media->is_video) {
        $instance->type = 'video';
        $instance->videoStandardResolutionUrl = $media->video_url;
        $instance->videoViews = $media->video_view_count;
    }

    if (isset($media->caption_is_edited)) {
      $instance->captionIsEdited = $media->caption_is_edited;
    }

    if (isset($media->is_ad)) {
      $instance->isAd = $media->is_ad;
    }

    $instance->createdTime = $media->taken_at_timestamp;
    $instance->shortcode = $media->shortcode;
    $instance->code = $media->shortcode;

    $instance->link = 'https://www.instagram.com/p/' . $instance->shortcode;

    $instance->commentsCount = 0;

    if (isset($media->edge_media_to_comment)) {
      if (isset($media->edge_media_to_comment->count)) {
        $instance->commentsCount = $media->edge_media_to_comment->count;
      }
    }

    if (isset($media->edge_media_to_parent_comment)) {
      if (isset($media->edge_media_to_parent_comment->count)) {
        $instance->commentsCount = $media->edge_media_to_parent_comment->count;
      }
    }

    $instance->likesCount = $media->edge_media_preview_like->count;
    $images = $this->getImageUrlsFromDisplayResources($media->display_resources);
    $instance->imageStandardResolutionUrl = $images['standard'];
    $instance->imageLowResolutionUrl = $images['low'];
    $instance->imageHighResolutionUrl = $images['high'];
    $instance->imageThumbnailUrl = $images['thumbnail'];

    if (isset($media->edge_media_to_caption->edges[0]->node->text)) {
      $instance->caption = $media->edge_media_to_caption->edges[0]->node->text;
    }

    if (isset($media->location->id)) {
      $instance->locationId = $media->location->id;
    }

    if (isset($media->location->name)) {
      $instance->locationName = $media->location->name;
    }

    $instance->owner = $media->owner;
    $instance->ownerId = $media->owner->id;

    $instance->carouselMedia = $this->processCarousel($media);

    return $instance;
  }

  private function processCarousel ($media) {
    if (!isset($media->edge_sidecar_to_children)) return null;
    if (!isset($media->edge_sidecar_to_children->edges)) return null;

    $carouselMedia = [];
    $items = $media->edge_sidecar_to_children->edges;

    foreach ($items as $item) {
      $node = $item->node;

      $images = $this->getImageUrlsFromDisplayResources($node->display_resources);

      $data = (object) [
        'id' => $node->id,
        'shortcode' => $node->shortcode,
        'imageStandardResolutionUrl' => $images['standard'],
        'imageLowResolutionUrl' => $images['low'],
        'imageHighResolutionUrl' => $images['high'],
        'imageThumbnailUrl' => $images['thumbnail']
      ];

      $data->type = 'image';

      if ($node->is_video) {
          $data->type = 'video';
          $data->videoStandardResolutionUrl = $media->video_url;
          $data->videoViews = $media->video_view_count;
      }

      $carouselMedia[] = $data;
    }

    return $carouselMedia;
  }

  private function getImageUrlsFromDisplayResources($displayResources) {
    $urls = [];

    foreach ($displayResources as $image) {
      if ($image->config_width == 640) {
        $urls['thumbnail'] = $image->src;
      }

      if ($image->config_width == 640) {
        $urls['low'] = $image->src;
      }

      if ($image->config_width == 750) {
        $urls['standard'] = $image->src;
      }

      if ($image->config_width == 1080) {
        $urls['high'] = $image->src;
      }
    }

    return $urls;
  }
}
