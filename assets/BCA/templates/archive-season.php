<?php
use BCA\ChanceTheater\Helpers;

$series = get_term_children(get_queried_object_id(), 'season');
?>

<article>
  <header>
    <h1><?php single_cat_title() ?></h1>
    <div class="lead">
      <?php echo term_description() ?>
    </div>
  </header>
  <hr>

  <?php if (!have_posts()) : ?>
    <div class="alert">
      <?php _e('Sorry, this season contains no shows.', 'roots'); ?>
    </div>
    <?php get_search_form(); ?>
  <?php endif; ?>

  <?php foreach($series as $series_id) : ?>
  <?php $series = get_term($series_id, 'season'); ?>
  <div class="productions">


    <h2>
      <?php echo $series->name ?>
      <?php if (of_get_option('subscriptions_btn_enabled')) : ?>
        <?php $permalink_s = home_url(get_page_uri(of_get_option('subscriptions_btn_link_id'))) ?>
        <?php $permalink_m = home_url(get_page_uri(of_get_option('memberships_btn_link_id'))) ?>
        <?php if (strpos($series->name, 'OTR') === false) : ?>
        <a href="<?php echo $permalink_s ?>" class="btn btn-xs">
          <?php echo of_get_option('subscriptions_cta_link_text') ?>
        </a>
        <?php endif; ?>
        <a href="<?php echo $permalink_m ?>" class="btn btn-xs">
          <?php echo of_get_option('memberships_cta_link_text') ?>
        </a>
      <?php endif; ?>
    </h2>

    <?php while (have_posts()) : the_post(); ?>
    <?php if (has_term($series_id, 'season', $post->ID)) : ?>
    <div class="production">
      <a href="<?php echo get_permalink($post->ID) ?>">
      <?php
        $date_opening = new DateTime('@'.$post->__get('date-opening'));
        $date_opening->setTimezone(Helpers::getTimezone());
        $date_closing = new DateTime('@'.$post->__get('date-closing'));
        $date_closing->setTimezone(Helpers::getTimezone());

        $season_header_id = $post->__get('img-season-header');
        if (!$season_header_id) {
          $season_header_id = get_post_thumbnail_id();
        }
        echo wp_get_attachment_image(
          $season_header_id,
          'production-season-header',
          null,
          array('class'=>'img-responsive production-header')
        );
      ?>
      </a>
      <div class="production-inner">
        <h3 class="production-heading">
          <a href="<?php echo get_permalink($post->ID) ?>">
            <?php echo Helpers::bootstrapHeading(get_the_title()) ?>
          </a>
        </h3>
        <div class="production-dates">
          <time datetime="<?php echo $date_opening->format(DateTime::RFC3339) ?>">
            <?php echo $date_opening->format('F j') ?>
          </time>
          &mdash;
          <time datetime="<?php echo $date_closing->format(DateTime::RFC3339) ?>">
            <?php echo $date_closing->format('F j') ?>
          </time>
        </div>
        <div class="production-excerpt">
          <?php the_excerpt() ?>
        </div>
      </div>
      <?php if ($post->__get('ticketing-link')) : ?>
      <a href="<?php echo $post->__get('ticketing-link') ?>" class="btn btn-primary btn-xs">
        Buy Tickets
      </a>
      <?php endif;?>
      <a href="<?php echo get_permalink($post->ID) ?>" class="btn btn-xs btn-secondary">
        Read More
      </a>
    </div>
    <?php endif; ?>
    <?php endwhile; ?>
  </div>
  <?php endforeach; ?>

  <div class="row">
  <div class="col-xs-12"><?php get_template_part('templates/subscribe-cta') ?></div>
  </div>

  <?php if ($wp_query->max_num_pages > 1) : ?>
    <nav class="post-nav">
      <ul class="pager">
        <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
        <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
      </ul>
    </nav>
  <?php endif; ?>
</article>
