<aside class="post-headline hentry">
  <h3 class="entry-title">
    <a href="<?php the_permalink() ?>">
      <?php the_title() ?>
    </a>
  </h3>
  <div class="entry-summary">
    <?php the_excerpt() ?>
  </div>
  <ul class="entry-meta">
    <li class="published">
      <?php the_time('F j, Y') ?>
    </li>
    <li class="author">
      <?php the_author_posts_link() ?>
    </li>
    <li class="bookmark extra">
      <a href="<?php the_permalink() ?>" title="Permalink">View Post</a>
    </li>
    <li class="extra">
      <a href="<?php the_permalink() ?>#comments" title="<?php comments_number('Be the first!', 'One Comment', '%s Comments'); ?>">
         Comments
      </a>
    </li>
  </ul>
</aside>