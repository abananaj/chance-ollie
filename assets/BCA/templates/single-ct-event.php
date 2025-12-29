<?php
  use BCA\ChanceTheater\Helpers;

  while (have_posts()) : the_post();

  $date_start = new DateTime('@'.$post->__get('date-start'));
  $date_start->setTimezone(Helpers::getTimezone());

  $date_end = new DateTime('@'.$post->__get('date-end'));
  $date_end->setTimezone(Helpers::getTimezone());
?>
  <div class="row">
    <div class="col-md-7">
      <?php if ($post->description) : ?>
      <h2>About This Event</h2>
      <?php echo do_shortcode($post->description) ?>
      <?php endif; ?>
      <?php if ($post->production) : ?>
      <?php $production = get_post($post->production) ?>
      <h2>About <?php echo $production->post_title ?></h2>
      <?php echo do_shortcode($production->post_content) ?>
      <br><br>
      <p>
        <a href="<?php echo get_permalink($post->production) ?>" class="btn btn-primary btn-sm">
          <strong>Learn More</strong> about <?php echo $production->post_title ?>
        </a>
      </p>
      <?php endif; ?>
    </div>
    <div class="col-md-5">

      <h2>At a Glance</h2>
      <dl>
        <dt>Approximate Event Length:</dt>
        <dd><?php echo Helpers::getTimespan($date_start->format('U'), $date_end->format('U')) ?></dd>
        <?php $types = get_the_terms(get_the_ID(), 'event-type') ?>
        <?php if ($types) : ?>
        <dt>Event Categories:</dt>
        <?php foreach ($types as $type) : ?>
        <dd>
        <i class="fa fa-angle-right"></i>
          <?php echo $type->name ?>
        </dd>
        <?php endforeach; ?>
        <?php endif; ?>
      </dl>
      <?php if ($post->venue) : ?>
      <h2>Venue</h2>
      <?php echo Helpers::getVenueHcard($post->venue) ?>
      <a href="<?php echo get_permalink($post->venue) ?>" class="btn btn-xs btn-primary">
        More Info
      </a>
      <a href="<?php echo get_permalink($post->venue) ?>" class="btn btn-xs btn-default">
        Get Directions
      </a>
      <?php endif; ?>
    </div>
  </div>

<?php endwhile; ?>
