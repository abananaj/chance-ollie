<aside class="media hentry">
  <a class="thumb-link pull-left" href="<?php the_permalink() ?>">
    <?php the_post_thumbnail('square-100', array('class'=>'media-object img-thumbnail')) ?>
  </a>
  <div class="media-body entry-title">
    <h4 class="media-heading">
      <a href="<?php the_permalink() ?>" title="Permalink"><?php the_title() ?></a>
    </h4>
    <div class="text-muted">
      <span class="published">
        <?php the_time('F j, Y') ?>
      </span>
    </div>
    <?php the_excerpt() ?>
  </div>
</aside>
