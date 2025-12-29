<article <?php post_class(); ?>>
  <div class="row">
    <div class="col-md-3 post-icon"></div>
    <div class="col-md-9">
      <header class="page-header">
        <?php if (isset($_GET['production'])) : ?>
        <h1>
          Show Archive
          <small><?php echo get_the_title($_GET['production']) ?></small>
        </h1>
        <?php else : ?>
        <h1><?php echo roots_title(); ?></h1>
        <?php endif; ?>
      </header>
    </div>
  </div>
  <div class="row">
    <div class="col-md-9 col-md-push-3">
      <?php while (have_posts()) : the_post(); setup_postdata($post); ?>
        <?php get_template_part('templates/headline', get_post_format()); ?>
      <?php endwhile; ?>
      <?php if ($wp_query->max_num_pages > 1) : ?>
        <nav class="post-nav">
          <ul class="pager">
            <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
            <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
    <div class="col-md-3 col-md-pull-9 sidebar-post">
      <a href="<?php echo get_permalink(get_page_by_path('blog')); ?>" class="link-blog-home">
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x"></i>
          <i class="fa fa-home fa-stack-1x fa-inverse"></i>
        </span>
        Blog Home
      </a>
      <?php dynamic_sidebar('sidebar-post'); ?>
    </div>
  </div>
</article>
