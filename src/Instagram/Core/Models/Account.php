<?php

namespace Instagram\Core\Models;

class Account
{

  /**
   * Convert from an API
   */
  public function convert ($user) {
    $instance = new self();
    $instance->username = isset($user->username) ? $user->username : null;
    $instance->followsCount = isset($user->following_count) ? $user->following_count : null;
    $instance->followedByCount = isset($user->follower_count) ? $user->follower_count : null;
    $instance->profilePicUrl = isset($user->profile_pic_url) ? $user->profile_pic_url : null;
    $instance->id = isset($user->pk) ? $user->pk : null;
    $instance->biography = isset($user->biography) ? $user->biography : null;
    $instance->fullName = isset($user->full_name) ? $user->full_name : null;
    $instance->mediaCount = isset($user->media_count) ? $user->media_count : null;
    $instance->isPrivate = isset($user->is_private) ? $user->is_private : null;
    $instance->externalUrl = isset($user->external_url) ? $user->external_url : null;
    $instance->isVerified = isset($user->is_verified) ? $user->is_verified : null;

    // Unset fields that are null and was never set.
    foreach ($instance as $key => $val) {
      if (is_null($val)) unset($instance->{$key});
    }

    return $instance;
  }

  /**
   * Convert from the ?__a=1 page
   */
  public function convertFromPage ($user) {
    $instance = new self();
    $instance->username = isset($user->username) ? $user->username : null;
    $instance->followsCount = isset($user->edge_follow) ? $user->edge_follow->count : null;
    $instance->followedByCount = isset($user->edge_followed_by) ? $user->edge_followed_by->count : null;
    $instance->profilePicUrl = isset($user->profile_pic_url) ? $user->profile_pic_url : null;
    $instance->id = isset($user->id) ? $user->id : null;
    $instance->biography = isset($user->biography) ? $user->biography : null;
    $instance->fullName = isset($user->full_name) ? $user->full_name : null;
    $instance->mediaCount = isset($user->edge_owner_to_timeline_media) ? $user->edge_owner_to_timeline_media->count : null;
    $instance->isPrivate = isset($user->is_private) ? $user->is_private : null;
    $instance->externalUrl = isset($user->external_url) ? $user->external_url : null;
    $instance->isVerified = isset($user->is_verified) ? $user->is_verified : null;

    // Unset fields that are null and was never set.
    foreach ($instance as $key => $val) {
      if (is_null($val)) unset($instance->{$key});
    }

    return $instance;
  }
}
