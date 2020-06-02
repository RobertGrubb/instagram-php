<?php

namespace Instagram\Core\Resources;

class GraphQueries {

  private $list;

  public function __construct () {

    $this->list = [

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
