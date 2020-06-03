<?php

namespace Instagram\Core\Models;

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
  public function convert ($user) {
    $instance = new self();
    $instance->username = $user->username;
    $instance->followsCount = $user->following_count;
    $instance->followedByCount = $user->follower_count;
    $instance->profilePicUrl = $user->profile_pic_url;
    $instance->id = $user->pk;
    $instance->biography = $user->biography;
    $instance->fullName = $user->full_name;
    $instance->mediaCount = $user->media_count;
    $instance->isPrivate = $user->is_private;
    $instance->externalUrl = $user->external_url;
    $instance->isVerified = $user->is_verified;
    return $instance;
  }
}
