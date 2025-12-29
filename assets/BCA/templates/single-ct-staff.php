<?php while (have_posts()) : the_post(); ?>
<div class="entry-content">
  <h2 class="sr-only">Staff Member Profile</h2>
  <br>
  <?php the_content(); ?>
</div>
<?php endwhile; ?>
