<?php

namespace Instagram\Models;

class Account
{
  public $id = null;
  public $username = null;
  public $fullName = null;
  public $profilePicUrl = null;
  public $biography = null;
  public $externalUrl = null;
  public $followsCount = null;
  public $followedByCount = null;
  public $mediaCount = null;
  public $isPrivate = null;
  public $isVerified = null;

  /**
   * Set based on endpoint
   */
  public function set ($endpoint, $user) {
    $instance = new self();

      print_r($user);


    switch ($endpoint) {

      case 'User/AccountData':
        $instance->username = $user['username'];
        $instance->followsCount = $user['edge_follow']['count'];
        $instance->followedByCount = $user['edge_followed_by']['count'];
        $instance->profilePicUrl = $user['profile_pic_url'];
        $instance->id = $user['id'];
        $instance->biography = $user['biography'];
        $instance->fullName = $user['full_name'];
        $instance->mediaCount = $user['edge_owner_to_timeline_media']['count'];
        $instance->isPrivate = $user['is_private'];
        $instance->externalUrl = $user['external_url'];
        $instance->isVerified = $user['is_verified'];
        break;
    }

    return $instance;
  }
}
