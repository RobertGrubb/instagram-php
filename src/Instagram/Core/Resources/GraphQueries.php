<?php

namespace Instagram\Core\Resources;

class GraphQueries {

  private $list;

  public function __construct () {

    $this->list = [

      'user' => [
        'query_hash' => 'ad99dd9d3646cc3c0dda65debcd266a7',
        'variables' => [
          'username' => null,
          'user_id' => null,
          'include_chaining' => false,
          'include_reel' => true,
          'include_suggested_users' => false,
          'include_logged_out_extras' => false,
          'include_highlight_reels' => false,
          'include_related_profiles' => false
        ]
      ],

      'media' => [
        'query_hash' => '477b65a610463740ccdb83135b2014db',
        'variables' => [
          'shortcode' => '',
          'child_comment_count'   => 0,
          'fetch_comment_count'   => 0,
          'parent_comment_count'  => 0,
          'has_threaded_comments' => false
        ]
      ],

      'feed' => [
        'query_hash' => '44efc15d3c13342d02df0b5a9fa3d33f',
        'variables' => [
          'id' => '',
          'first' => 12
        ]
      ],

    ];

  }

  public function get ($query) {
    if (isset($this->list[$query])) return $this->list[$query];
    return false;
  }

}
