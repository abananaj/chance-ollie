<?php while (have_posts()) : the_post(); ?>
<div class="entry-content">
  <?php
    if (
      $post->__get('supporter-type') === 'institutional'
      && has_post_thumbnail()
    ) {
      echo get_the_post_thumbnail(null, 'mini', array('class'=>'pull-right'));
    }
  ?>
  <h2 class="sr-only">Supporter Profile</h2>
  <br>
  <?php the_content(); ?>
</div>
<?php endwhile; ?>
