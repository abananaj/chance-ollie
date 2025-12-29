<?php
  $post_type_id = get_post_type();

  $post_type = $post_type_id;
  if (of_get_option('section_type_'.$post_type)) {
    $post_type = of_get_option('section_type_'.$post_type);
  }

  $post_type_title = of_get_option('section_title_'.$post_type);
?>
<section class="subnav subnav-<?php echo $post_type ?>">

  <header class="pull-left">
    <?php if (is_singular() && $post_type === 'ct-production' && has_term('', 'season')) : ?>
      <?php
        $series = get_the_terms(get_the_ID(), 'season');
        $series = array_pop($series);

        $series_id = $series->term_id;
        if (isset($series->parent) && $series->parent !== 0) {
          $series_id = $series->parent;
        }
        $series_parent = get_term($series_id, 'season');
      ?>
      <?php if (isset($series->parent)) : ?>
      <a href="<?php echo get_term_link($series_id, 'season') ?>">
        <?php echo $series_parent->name ?>
        <small><?php echo $series->name ?></small>
      </a>
      <?php else : ?>
      <a href="<?php echo get_term_link($series_id, 'season') ?>">
        <?php echo $series->name ?>
      </a>
      <?php endif; ?>
    <?php elseif (get_post_type_archive_link($post_type)) : ?>
      <a href="<?php echo get_post_type_archive_link($post_type) ?>">
      <?php echo $post_type_title ?>
      </a>
    <?php elseif ($post->post_name == 'blog') : ?>
      <?php echo of_get_option('section_title_post'); ?>
    <?php else: ?>
      <?php echo $post_type_title ?>
    <?php endif; ?>
  </header>
  <?php if (has_nav_menu('subnav-'.$post_type)) : ?>
    <nav>
    <?php wp_nav_menu(array('theme_location' => 'subnav-'.$post_type, 'menu_class' => 'pull-left')) ?>
    </nav>
  <?php endif; ?>
  <div class="clearfix"></div>
</section>
