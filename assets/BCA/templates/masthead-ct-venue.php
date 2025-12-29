<?php
  use BCA\ChanceTheater\Helpers;
?>
<header class="masthead-inner">
  <img src="<?php echo Helpers::getVenueMapSrc(get_the_id(), 'square-150') ?>" class="pull-left wp-post-image">
  <div class="pull-left">
    <h1>
      <?php echo roots_title(); ?>
    </h1>
    <strong><?php echo Helpers::getVenueHcard(get_the_id()) ?></strong>
    <?php echo Helpers::getReferrerButton() ?>
  </div>
  <div class="clearfix"></div>
</header>