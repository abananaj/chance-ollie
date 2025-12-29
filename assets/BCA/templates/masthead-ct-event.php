<?php
  use BCA\ChanceTheater\Helpers;

  $thumb_id = null;
  if (!has_post_thumbnail()) {
    $thumb_id = $post->production;
  }

  $date_start = new DateTime('@'.$post->__get('date-start'));
  $date_start->setTimezone(Helpers::getTimezone());

  $date_end = new DateTime('@'.$post->__get('date-end'));
  $date_end->setTimezone(Helpers::getTimezone());
?>
<header class="masthead-inner">
  <?php echo get_the_post_thumbnail($thumb_id, 'mini', array('class'=>'pull-left hidden-xs')) ?>
  <div class="pull-left">
    <h1>
      <?php echo roots_title(); ?>
    </h1>
    <time datetime="<?php echo $date_start->format(DateTime::RFC3339) ?>">
      <?php echo $date_start->format('l, F j, Y') ?><br>
      <?php echo $date_start->format('g:i a') ?>
    </time>&mdash;
    <time datetime="<?php echo $date_start->format(DateTime::RFC3339) ?>">
      <?php echo $date_end->format('g:i a') ?>
    </time>
    <?php if ($post->__get('ticketing-link')) : ?>
    <br><br>
    <a href="<?php echo $post->__get('ticketing-link') ?>" class="btn btn-primary btn-sm">
      Buy Tickets
    </a>
    <?php endif; ?>
  </div>
  <div class="clearfix"></div>
</header>