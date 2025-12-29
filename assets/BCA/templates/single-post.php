<?php while (have_posts()) : the_post(); setup_postdata($post); ?>
  <article <?php post_class(); ?>>
    <div class="row">
      <div class="col-md-3 post-icon"></div>
      <div class="col-md-9">
        <header class="page-header">
          <h1 class="entry-title"><?php the_title(); ?></h1>
          <?php get_template_part('templates/entry-meta'); ?>
        </header>
      </div>
    </div>
    <div class="row">
      <div class="col-md-9 col-md-push-3 entry-content">
        <?php get_template_part('templates/content', get_post_format()); ?>
        <footer>
          <div class="social-icons">
            <div class="g-plusone" data-size="medium" data-annotation="none" data-expandTo="bottom" data-href="<?php echo htmlspecialchars(get_permalink($post->ID)) ?>"></div>&nbsp;
            <a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-text="<?php echo get_the_title() ?>" data-url="<?php echo htmlspecialchars(get_permalink($post->ID)) ?>" data-via="<?php echo htmlspecialchars(of_get_option('social_id_twitter')) ?>" data-related="<?php echo htmlspecialchars(of_get_option('social_id_twitter')) ?>">Tweet</a>
            <div class="fb-like" data-href="<?php echo htmlspecialchars(get_permalink($post->ID)) ?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
          </div>
          <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
        </footer>
        <?php comments_template('/templates/comments.php'); ?>
      </div>
      <div class="col-md-3 col-md-pull-9 sidebar-post">
        <a href="<?php echo get_year_link(''); ?>" class="link-blog-home">
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
<?php endwhile; ?>
