<?php
  use BCA\ChanceTheater\Helpers;

  $ancestors = get_ancestors(get_the_ID(), 'page');
  $parent = get_the_ID();
  if ($ancestors) {
    $parent = end($ancestors);
  }

  $menu_name = 'subnav-page-'.$parent;
?>
<?php if (has_nav_menu($menu_name)) : ?>
<section class="subnav subnav-<?php echo $post_type ?>">
  <header class="pull-left">
    <a href="<?php echo get_permalink($parent) ?>">
    <?php echo Helpers::getNavMenuTitle($menu_name) ?>
    </a>
  </header>
  <nav>
    <?php wp_nav_menu(array('theme_location' => $menu_name, 'menu_class' => 'pull-left')) ?>
  </nav>
  <div class="clearfix"></div>
</section>
<?php endif; ?>
