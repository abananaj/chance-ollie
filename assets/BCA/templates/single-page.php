<article>
  <header class="page-header">
    <h1>
      <?php echo roots_title(); ?>
    </h1>
    <?php $crumbs = get_ancestors(get_the_ID(), 'page') ?>
    <?php if ($crumbs) : ?>
    <ol class="breadcrumb">
      <?php foreach ($crumbs as $crumb) : ?>
      <li>
        <a href="<?php echo get_permalink($crumb) ?>" title="<?php echo get_the_title($crumb) ?>">
          <?php echo (get_post_meta($crumb, 'title-breadcrumb')) ? htmlspecialchars(get_post_meta($crumb, 'title-breadcrumb', true)) : get_the_title($crumb) ?>
        </a>
      </li>
      <?php endforeach; ?>
      <li class="active" title="<?php echo get_the_title() ?>">
        <?php echo (get_post_meta($post->ID, 'title-breadcrumb')) ? htmlspecialchars(get_post_meta($post->ID, 'title-breadcrumb', true)) : get_the_title() ?>
      </li>
    </ol>
    <?php endif; ?>
  </header>
  <?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
    <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
  <?php endwhile; ?>
</article>
