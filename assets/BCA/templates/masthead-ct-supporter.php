<header class="masthead-inner">
  <?php
    if (
      $post->__get('supporter-type') === 'individual'
      && has_post_thumbnail()
    ) {
      echo get_the_post_thumbnail(null, 'square-100', array('class' => 'pull-left'));
    }
  ?>
  <h1>
    <?php the_title(); ?>
  </h1>
  <p>
  <?php
    $board_position = get_term($post->__get('board-position'), 'ct-board-positions');
    $level = get_term($post->__get('donor-level'), 'ct-supporter-level');
    $subtitle = array();
    if (isset($board_position->name)) {
      $subtitle[] = $board_position->name;
    }
    if (isset($level->name) && $post->__get('display-donors')) {
      $subtitle[] = $level->name;
    }
    echo implode(' / ', $subtitle);
  ?>
  </p>
  <div class="clearfix"></div>
</header>
