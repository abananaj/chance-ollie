<?php
  use BCA\ChanceTheater\Helpers;

  while (have_posts()) : the_post();
  $meta = get_post_meta(get_the_id());
?>
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-7">
      <h2>About The Venue</h3>
      <?php the_content(); ?>
    </div>
    <div class="col-md-5">
      <div class="venue-amenities">
        <?php $amenities = Helpers::getVenueAmenitiesList(get_the_id()) ?>
        <?php if ($amenities) : ?>
        <h2>Venue Amenities</h2>
        <?php echo $amenities ?>
      <?php endif; ?>
      </div>
    </div>
  </div>
  <?php if (isset($meta['directions'][0])) : ?>
  <div class="row">
    <div class="col-md-7">
      <div class="venue-directions">
        <h2>Driving Directions</h2>
        <?php echo $meta['directions'][0] ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
<?php endwhile; ?>
