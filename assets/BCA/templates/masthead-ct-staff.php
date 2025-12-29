<?php
  use BCA\ChanceTheater\Helpers;
?>
<header class="masthead-inner">
  <?php
    if (has_post_thumbnail()) {
      echo get_the_post_thumbnail(null, 'square-125', array('class' => 'pull-left'));
    }
  ?>
  <h1>
    <?php the_title(); ?><br>
    <small><?php echo get_post_meta(get_the_id(), 'title', true) ?></small>
  </h1>

  <?php echo Helpers::getReferrerButton() ?>
  <div class="clearfix"></div>
</header>
