<?php

namespace Instagram\Core\Validations;

use Instagram\Core\Exceptions\InstagramException;

/**
 * Validation takes in the raw data, verifies
 * that the payload includes what it should,
 * then returns the specific part of the data
 * it's supposed to return.
 *
 * Based on the endpoint key.
 */
class AccountValidation
{

  public function run ($endpoint, $user) {

    $data = false;

    switch ($endpoint) {

      case 'user/account/json':

        if (!isset($user->graphql)) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($user->graphql->user)) {
            throw new InstagramException('Data response incorrect');
        }

        return $user->graphql->user;
        break;

        case 'user/medias/page':

          if (!isset($user['entry_data'])) {
              throw new InstagramException('Data response incorrect');
          }

          if (!isset($user['entry_data']['ProfilePage'])) {
              throw new InstagramException('Data response incorrect');
          }

          if (!isset($user['entry_data']['ProfilePage'][0])) {
              throw new InstagramException('Data response incorrect');
          }

          if (!isset($user['entry_data']['ProfilePage'][0]['graphql'])) {
              throw new InstagramException('Data response incorrect');
          }

          if (!isset($user['entry_data']['ProfilePage'][0]['graphql']['user'])) {
              throw new InstagramException('Data response incorrect');
          }

          $userData = $user['entry_data']['ProfilePage'][0]['graphql']['user'];

          if (!isset($userData['edge_owner_to_timeline_media'])) {
            throw new InstagramException('Data response incorrect');
          }

          if (!isset($userData['edge_owner_to_timeline_media']['edges'])) {
            throw new InstagramException('Data response incorrect');
          }

          $data = $userData;

          break;

      case 'user/account/page':

        if (!isset($user['entry_data'])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($user['entry_data']['ProfilePage'])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($user['entry_data']['ProfilePage'][0])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($user['entry_data']['ProfilePage'][0]['graphql'])) {
            throw new InstagramException('Data response incorrect');
        }

        if (!isset($user['entry_data']['ProfilePage'][0]['graphql']['user'])) {
            throw new InstagramException('Data response incorrect');
        }

        $data = $user['entry_data']['ProfilePage'][0]['graphql']['user'];
        break;
    }

    return $data;
  }
}
