<?php

namespace Instagram\Core\Models;

class AccountMedias
{

  public $owner = null;
  public $medias = null;

  /**
   * Set based on endpoint
   */
  public function set ($endpoint, $user) {
    $instance = new self();


    switch ($endpoint) {

      case 'user/medias/page':
        $userData = new Account;
        $instance->owner = $userData->set('user/account/page', $user);

        $nodes = $user['edge_owner_to_timeline_media']['edges'];

        foreach($nodes as $post) {
          $post = (array)$post;
          $post = $post['node'];
          $media = $this->createMediaObject($post);
          $media->owner = $instance->owner;

          $instance->medias[] = $media;
        }

        break;
    }

    return $instance;
  }

  public function createMediaObject($media) {
    $mediaObject = new Media;
    return $mediaObject->set('account-media/page', $media);
  }
}
